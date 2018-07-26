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
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $jobs = $this->storage->getRepository(Job::class)->findAll();
        $data = array_map(function (Job $job) {
            return $job->jsonSerialize();
        }, $jobs);
        return new HtmlResponse($this->template->render('app::job-list-page', ['jobs' => $data]));
    }
}
