<?php

namespace App\Command;

use App\Entity\Job;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobExec extends CommandBase
{

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName('job:exec')
            ->setDescription('Execute job')
            ->addArgument(
                'name_or_id',
                InputArgument::REQUIRED,
                'Job name or id'
            );
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name_or_id = $input->getArgument('name_or_id');
        $prop = is_numeric($name_or_id) ? 'id' : 'name';
        $job = $this->storage->getRepository(Job::class)->findOneBy([$prop => $name_or_id]);
        if ($job instanceof Job) {
            $output->writeln(
                sprintf($job->execute() ? '<info>Job run success.</info>' : '<error>Job run error.</error>')
            );
        } else {
            $output->writeln(sprintf('<error>Job with %s "%s" is not found.</error>', $prop, $name_or_id));
        }
    }
}
