<?php

namespace App\Command;

use App\Entity\Job as JobEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobRemove extends CommandBase
{
    public const NAME = 'job:remove';

  /**
   * Configures the command
   */
    protected function configure()
    {
        $this
        ->setName(self::NAME)
        ->setDescription('Remove a job')
        ->addArgument(
            'name_or_id',
            InputArgument::REQUIRED,
            'Job name or id'
        );
    }

  /**
   * Executes the current command
   */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name_or_id = $input->getArgument('name_or_id');
        $prop = is_numeric($name_or_id) ? 'id' : 'name';
        $job = $this->storage->getRepository(JobEntity::class)->findOneBy([$prop => $name_or_id]);
        if ($job instanceof JobEntity) {
            $this->storage->remove($job);
            $this->storage->flush();
            $output->writeln(sprintf('<info>Job "%s" removed.</info>', $job->getName()));
            return Command::SUCCESS;
        } else {
            $output->writeln(sprintf('<error>Job with %s "%s" is not found.</error>', $prop, $name_or_id));
            return Command::FAILURE;
        }
    }
}
