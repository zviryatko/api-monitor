<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Job;
use App\Form\Validator\CsrfGuard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Session\SessionMiddleware;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Hidden;

class JobAddHandler extends BasePageHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = (new AnnotationBuilder())->createForm(Job::class);
        /** @var \Zend\Expressive\Csrf\SessionCsrfGuard $guard */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        /** @var \Zend\Expressive\Session\LazySession $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if (!$session->has('__csrf')) {
            $guard->generateToken();
        }
        $token = $session->get('__csrf');
        $form->add(new Hidden('_csrf', ['value']));
        $form->getInputFilter()->add([
            'name' => '_csrf',
            'required' => true,
            'validators' => [new CsrfGuard($guard)],
        ]);

        // Validate PSR-7 request and get a validation result
        if ($request->getMethod() === 'POST') {
            $form->setData($request->getParsedBody());
            if ($form->isValid()) {
                $job = (new \Zend\Hydrator\Reflection)->hydrate($form->getData(), new Job('', ''));
                $this->storage->persist($job);
                $this->storage->flush();
                return new RedirectResponse($this->router->generateUri('job.list'));
            }
        }

        return new HtmlResponse($this->template->render('app::job-add-page', [
            'token' => $token,
            'errors' => $form->getMessages(),
        ]));
    }
}
