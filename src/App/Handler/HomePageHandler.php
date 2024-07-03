<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Project;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class HomePageHandler extends BasePageHandler implements RequestHandlerInterface
{
    use AuthorizationTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->isAuthorized($request)) {
            return new HtmlResponse($this->template->render('app::promo'));
        }
        $repo = $this->storage->getRepository(Project::class);
        $public = $repo->findBy(['public' => true]);
        $projects = $repo->findBy(['owner' => $this->getProfile($request)->id()]);
        return new HtmlResponse($this->template->render('app::home', [
            'public' => $public,
            'own' => $projects,
        ]));
    }
}
