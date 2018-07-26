<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Job;
use App\Entity\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class HomePageHandler extends BasePageHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $jobs = $this->storage->getRepository(Job::class)->findAll();
        $logs = $this->storage->getRepository(Log::class);
        $data = array_map(function (Job $job) use ($logs) {
            return [
                'id' => $job->getId(),
                'name' => $job->getName(),
                'logs' => array_map(function (Log $log) {
                    return $log->jsonSerialize();
                }, $logs->findBy(['job' => $job->getId()], ['created' => 'DESC'], 100)),
            ];
        }, $jobs);
        return new HtmlResponse($this->template->render('app::home-page', ['data' => $data]));
    }
}
