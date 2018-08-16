<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Job;
use App\Entity\Log;
use App\Entity\Project;
use App\Form\Validator\CsrfGuard;
use Doctrine\ORM\Query\Expr\Join;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Csrf\CsrfGuardInterface;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Session\SessionMiddleware;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Submit;
use Zend\Form\FormInterface;
use Zend\Validator\InArray;

class JobFormHandler extends BasePageHandler implements RequestHandlerInterface
{
    use AuthorizationTrait;
    use CsrfGeneratorTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->isAuthorized($request)) {
            return new HtmlResponse($this->template->render('error::403'), 403);
        }
        $job = $this->getJob($request);
        /** @var CsrfGuardInterface $guard */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $token = $this->getCsrfToken($request);
        $form = $this->getForm($job, $guard);

        // Validate PSR-7 request and get a validation result
        $submitted = false;
        $input = $request->getParsedBody();
        if ($request->getMethod() === 'POST') {
            $submitted = true;
            $form->setData($input);
            if ($input['op'] === 'delete' && $guard->validateToken($input['_csrf'])) {
                $logs = $this->storage->getRepository(Log::class)->findAll();
                foreach ($logs as $log) {
                    $this->storage->remove($log);
                }
                $this->storage->remove($job);
                $this->storage->flush();
                return new RedirectResponse($this->router->generateUri('job.list'));
            } elseif ($form->isValid()) {
                $job = $form->getObject();
                $this->storage->persist($job);
                $this->storage->flush();
                return new RedirectResponse($this->router->generateUri('job.list'));
            }
            $token = $this->getCsrfToken($request);
        }
        $projects = $this->getAvailableProjects($request);

        return new HtmlResponse($this->template->render('app::job', [
            'token' => $token,
            'job' => [
                'id' => $job->getId(),
                'name' => $input['name'] ?? $job->getName(),
                'command' => $input['command'] ?? $job->getCommand(),
                'project' => $input['project'] ?? $job->getProject()->getId(),
            ],
            'projects' => $projects,
            'submitted' => $submitted,
            'errors' => $form->getMessages(),
        ]));
    }

    private function getForm(Job $job, CsrfGuardInterface $guard): FormInterface
    {
        $form = (new AnnotationBuilder())->createForm(Job::class);
        $form->setHydrator(new \Zend\Hydrator\Reflection);
        $form->bind($job);
        $form->add(new Hidden('_csrf'));
        if ($job->getId()) {
            $form->add(new Submit('op', ['value' => 'update']));
            $form->add(new Submit('op', ['value' => 'delete']));
        } else {
            $form->add(new Submit('op', ['value' => 'add']));
        }
        $form->getInputFilter()->add([
            'name' => 'name',
            'required' => true,
        ]);
        $form->getInputFilter()->add([
            'name' => 'command',
            'required' => true,
        ]);
        $form->getInputFilter()->add([
            'name' => '_csrf',
            'required' => true,
            'validators' => [new CsrfGuard($guard)],
        ]);
        $form->getInputFilter()->add([
            'name' => 'op',
            'required' => true,
            'validators' => [
                new InArray([
                    'haystack' => $job->getId() ? ['update', 'delete'] : ['add'],
                    'messageTemplates' => [
                        InArray::NOT_IN_ARRAY => 'Wrong action, please use Add, Update or Delete button.',
                    ],
                ]),
            ],
        ]);
        return $form;
    }

    private function getJob(ServerRequestInterface $request): Job
    {
        $id = $request->getAttribute('id');
        $profile = $this->getProfile($request);
        $project = $this->getDefaultProject($request);
        if ($id) {
            $job = $this->storage->find(Job::class, $id);
        } else {
            $job = new Job('', $profile, $project, '');
        }
        return $job;
    }

    private function getDefaultProject(ServerRequestInterface $request): Project
    {
        $profile = $this->getProfile($request);
        $project = $this->storage->getRepository(Project::class)->findOneBy(['owner' => $profile->id()]);
        if (!$project) {
            $project = new Project(
                sprintf('%s\'s Default Project', $profile->nickname()),
                $profile,
                sprintf('%s-default', $profile->nickname()),
                false
            );
            $this->storage->persist($project);
            $this->storage->flush();
        }

        return $project;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return Project[]
     */
    private function getAvailableProjects(ServerRequestInterface $request): array
    {
        $profile = $this->getProfile($request);
        return $this->storage->createQueryBuilder()
            ->select('project')
            ->from(Project::class, 'project')
            ->where('project.owner = :profile')
            ->setParameter('profile', $profile->id())
            ->getQuery()->execute();
    }
}
