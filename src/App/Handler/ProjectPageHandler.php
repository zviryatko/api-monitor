<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Job;
use App\Entity\Log;
use App\Entity\Project;
use Doctrine\ORM\Query\Expr\Join;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class ProjectPageHandler extends BasePageHandler implements RequestHandlerInterface
{
    use AuthorizationTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Project $project */
        $project = $this->storage->getRepository(Project::class)->findOneBy([
            'alias' => $request->getAttribute('alias'),
        ]);
        if (!$project) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }
        if (!$this->checkAccess($request, $project)) {
            return new HtmlResponse($this->template->render('error::403'), 403);
        }
        return new HtmlResponse($this->template->render('app::project-page', [
            'project' => $project,
            'jobs' => $this->getLogs($project, $this->getPrecise($request)),
            'precise' => $request->getQueryParams()['precise'] ?? 'hours',
        ]));
    }

    private function checkAccess(ServerRequestInterface $request, Project $project): bool
    {
        $profile = $this->getProfile($request);
        return $project->isPublic() || ($this->isAuthorized($request) && $profile->id() === $profile->id());
    }

    private function getPrecise(ServerRequestInterface $request): \DateTime
    {
        $precise = $request->getQueryParams()['precise'] ?? 'hours';
        $options = [
            'hours' => '4 hours ago',
            'day' => '1 day ago',
            'week' => '1 week ago',
            'month' => '1 month ago',
        ];
        return new \DateTime($options[$precise]);
    }

    private function getLogs(Project $project, \DateTime $time): array
    {

        $query = $this->storage->createQueryBuilder()
            ->select('log')
            ->from(Log::class, 'log')
            ->join(Job::class, 'job', Join::WITH, 'log.job = job.id')
            ->where('job.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('log.created', 'DESC')
            ->getQuery();
        $logs = $query->execute();
        $data = [];
        foreach ($logs as $log) {
            $job = $log->getJob()->getId();
            if (empty($data[$job])) {
                $data[$job] = $log->getJob()->jsonSerialize();
                $data[$job]['pie'] = ['up' => 0, 'down' => 0];
            }
            $data[$job]['logs'][] = $log->jsonSerialize();
            if ($log->isUp()) {
                $data[$job]['pie']['up']++;
            } else {
                $data[$job]['pie']['down']++;
            }
        }
        return $data;
    }
}
