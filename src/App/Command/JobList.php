<?php

namespace App\Command;

use App\Entity\Job;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobList extends CommandBase
{

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName('job:list')
            ->setDescription('List of jobs');
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Job[] $jobs */
        $jobs = $this->storage->getRepository(Job::class)->findAll();
        $table = (new Table($output))
            ->setHeaders(['Id', 'Name', 'Command'])
            ->setRows(array_map(function (Job $job) {
                return [
                    $job->getId(),
                    $job->getName(),
                    strlen($job->getCommand()) > 50 ? substr($job->getCommand(), 0, 50) . '...' : $job->getCommand(),
                ];
            }, $jobs));
        $table->render();
    }
}
