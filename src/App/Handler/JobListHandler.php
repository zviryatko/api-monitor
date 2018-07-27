<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Job;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class JobListHandler extends BasePageHandler implements RequestHandlerInterface
{

    use AuthorizationTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->isAuthorized($request)) {
            return new HtmlResponse($this->template->render('error::403'), 404);
        }
        $jobs = $this->storage->getRepository(Job::class)->findBy(['profile' => $this->getProfile($request)->id()]);
        $data = array_map(function (Job $job) {
            return $job->jsonSerialize();
        }, $jobs);
        return new HtmlResponse($this->template->render('app::jobs', ['jobs' => $data]));
    }
}
