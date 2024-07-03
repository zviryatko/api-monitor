<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Job;
use App\Entity\Log;
use App\Entity\Project;
use App\Form\Validator\CsrfGuard;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use DoctrineModule\Validator\ObjectExists;
use DoctrineORMModule\Form\Annotation\EntityBasedFormBuilder;
use Laminas\Form\Annotation\AttributeBuilder;
use Laminas\Hydrator\Aggregate\AggregateHydrator;
use Laminas\Hydrator\ReflectionHydrator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Csrf\CsrfGuardInterface;
use Mezzio\Csrf\CsrfMiddleware;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Submit;
use Laminas\Form\FormInterface;
use Laminas\Validator\InArray;

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
                /** @var Job $job */
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
                'url' => $input['url'] ?? $job->getUrl(),
                'project' => $input['project'] ?? $job->getProject()->getId(),
            ],
            'projects' => $projects,
            'submitted' => $submitted,
            'errors' => $form->getMessages(),
        ]));
    }

    private function getForm(Job $job, CsrfGuardInterface $guard): FormInterface
    {
        $builder = new EntityBasedFormBuilder($this->storage, new AttributeBuilder());
        $form = $builder->createForm($job);
        $hydrator = new AggregateHydrator();
        $hydrator->add(new ReflectionHydrator(), 2);
        $hydrator->add(new DoctrineObject($this->storage), 1);
        $form->setHydrator($hydrator);
        $form->bind($job);
        $form->add(new Hidden('_csrf'));
        $form->remove('profile');
        if ($job->getId()) {
            $form->add(new Submit('op', ['value' => 'update']));
            $form->add(new Submit('op', ['value' => 'delete']));
        } else {
            $form->add(new Submit('op', ['value' => 'add']));
        }
        $form->getInputFilter()
            ->remove('profile')
            ->add([
                'name' => 'name',
                'required' => true,
            ])
            ->add([
                'name' => 'url',
                'required' => true,
            ])
            ->add([
                'name' => '_csrf',
                'required' => true,
                'validators' => [new CsrfGuard($guard)],
            ])
            ->add([
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
            ])
            ->add([
                'name' => 'project',
                'required' => true,
                'validators' => [
                    new ObjectExists([
                        'object_repository' => $this->storage->getRepository(Project::class),
                        'fields' => ['id'],
                    ]),
                ],
            ])
        ;
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
                'Default Project',
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
     * @param ServerRequestInterface $request
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
