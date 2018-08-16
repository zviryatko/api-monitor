<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Project;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class ProjectListHandler extends BasePageHandler implements RequestHandlerInterface
{

    use AuthorizationTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->isAuthorized($request)) {
            return new HtmlResponse($this->template->render('error::403'), 404);
        }
        return new HtmlResponse($this->template->render('app::projects', [
            'projects' => $this->storage->getRepository(Project::class)->findBy([
                'owner' => $this->getProfile($request)->id()
            ]),
        ]));
    }
}
