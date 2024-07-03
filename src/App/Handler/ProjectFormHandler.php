<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Project;
use App\Form\Validator\CsrfGuard;
use DoctrineORMModule\Form\Annotation\EntityBasedFormBuilder;
use Laminas\Form\Annotation\AttributeBuilder;
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

class ProjectFormHandler extends BasePageHandler implements RequestHandlerInterface
{

    use AuthorizationTrait;
    use CsrfGeneratorTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->isAuthorized($request)) {
            return new HtmlResponse($this->template->render('error::403'), 403);
        }
        $project = $this->getProject($request);
        /** @var CsrfGuardInterface $guard */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $token = $this->getCsrfToken($request);
        $form = $this->getForm($project, $guard);

        // Validate PSR-7 request and get a validation result
        $submitted = false;
        $input = $request->getParsedBody();
        if ($request->getMethod() === 'POST') {
            $submitted = true;
            $form->setData($input);
            if ($form->isValid()) {
                /** @var Project $project */
                $project = $form->getObject();
                $this->storage->persist($project);
                $this->storage->flush();
                return new RedirectResponse($this->router->generateUri('project.list'));
            }
            $token = $this->getCsrfToken($request);
        }

        return new HtmlResponse($this->template->render('app::project', [
            'token' => $token,
            'project' => [
                'id' => $project->getId(),
                'name' => $input['name'] ?? $project->getName(),
                'alias' => $input['alias'] ?? $project->getAlias(),
                'public' => $input['public'] ?? $project->isPublic(),
            ],
            'submitted' => $submitted,
            'errors' => $form->getMessages(),
        ]));
    }

    private function getForm(Project $project, CsrfGuardInterface $guard): FormInterface
    {
        $builder = new EntityBasedFormBuilder($this->storage, new AttributeBuilder());
        $form = $builder->createForm($project);
        $form->setHydrator(new ReflectionHydrator());
        $form->bind($project);
        $form->add(new Hidden('_csrf'));
        $form->remove('owner');
        if ($project->getId()) {
            $form->add(new Submit('op', ['value' => 'update']));
        } else {
            $form->add(new Submit('op', ['value' => 'add']));
        }
        $form->getInputFilter()->add([
            'name' => 'name',
            'required' => true,
        ]);
        $form->getInputFilter()->add([
            'name' => 'alias',
            'required' => true,
        ]);
        $form->getInputFilter()->remove('owner');
        $form->getInputFilter()->add([
            'name' => 'public',
            'required' => false,
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
                    'haystack' => $project->getId() ? ['update'] : ['add'],
                    'messageTemplates' => [
                        InArray::NOT_IN_ARRAY => 'Wrong action, please use Add or Update button.',
                    ],
                ]),
            ],
        ]);
        return $form;
    }

    private function getProject(ServerRequestInterface $request): Project
    {
        $id = $request->getAttribute('id');
        $profile = $this->getProfile($request);
        if ($id) {
            $project = $this->storage->find(Project::class, $id);
        } else {
            $project = new Project(
                'Default Project',
                $profile,
                sprintf('%s-default', $profile->nickname()),
                false
            );
        }
        return $project;
    }
}
