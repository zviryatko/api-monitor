<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class HomePageHandler extends BasePageHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $precise = $request->getQueryParams()['precise'] ?? 'hours';
        $options = [
            'hours' => '4 hours ago',
            'day' => '1 day ago',
            'week' => '1 week ago',
            'month' => '1 month ago',
        ];
        $time = (new \DateTime($options[$precise]));
        $dql = 'SELECT log.id FROM App\Entity\Log as log WHERE log.created > :date';
        $results = $this->storage->createQuery($dql)
            ->setParameter('date', $time)
            ->execute();
        $logIds = array_map(function ($i) {
            return $i['id'];
        }, $results);
        /** @var Log[] $logs */
        $logs = $this->storage->getRepository(Log::class)->findBy(['id' => $logIds], ['created' => 'DESC']);
        $data = [];
        foreach ($logs as $log) {
            $job = $log->getJob()->getId();
            if (empty($data[$job])) {
                $data[$job] = $log->getJob()->jsonSerialize();
            }
            $data[$job]['logs'][] = $log->jsonSerialize();
        }
        return new HtmlResponse($this->template->render('app::home-page', ['data' => $data, 'precise' => $precise]));
    }
}
