<?php

namespace App\Command;

use App\Entity\Job;
use App\Entity\Log;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Amp\ParallelFunctions\parallelMap;
use function Amp\Promise\wait;

class MonitorRunAll extends CommandBase
{

  /**
   * Configures the command
   */
    protected function configure()
    {
        $this
        ->setName('monitor:all')
        ->setDescription('Run all jobs');
    }

  /**
   * Executes the current command
   */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->storage->getRepository(Job::class);
        /** @var Job[] $jobs */
        $jobs = $repo->findAll();
        $storage = $this->storage;
        $table = (new Table($output))
        ->setHeaders(['Name', 'Status']);
        foreach ($jobs as $job) {
            $status = $job->execute();
            $storage->persist(new Log($job, $status));
            $table->addRow([$job->getName(), (int) $status]);
        }

        $table->render();
        $this->storage->flush();
    }
}
