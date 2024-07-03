<?php

namespace App\Command;

use App\Entity\Job;
use App\Entity\Log;
use App\Service\JobExecute;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorRunAll extends CommandBase
{
    public const NAME = 'monitor:all';

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Run all jobs');
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->storage->getRepository(Job::class);
        /** @var Job[] $jobs */
        $jobs = $repo->findAll();
        $storage = $this->storage;
        $jobExecute = new JobExecute();
        $table = (new Table($output))
            ->setHeaders(['Name', 'Status']);
        foreach ($jobs as $job) {
            $status = $jobExecute->execute($job);
            $storage->persist(new Log($job, $status));
            $table->addRow([$job->getName(), (int)$status]);
        }

        $table->render();
        $this->storage->flush();
        return Command::SUCCESS;
    }
}
