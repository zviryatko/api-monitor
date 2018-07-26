<?php

namespace App\Command;

use App\Entity\Job;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobAdd extends CommandBase {

  /**
   * Configures the command
   */
  protected function configure() {
    $this
      ->setName('job:add')
      ->setDescription('Add job')
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'Job name'
      )
      ->addArgument(
        'exec',
        InputArgument::REQUIRED,
        'Job command to execute'
      );
  }

  /**
   * Executes the current command
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $name = $input->getArgument('name');
    $command = $input->getArgument('exec');
    $job = new Job($name, $command);
    $this->storage->persist($job);
    $this->storage->flush();
    $output->writeln(sprintf('<info>Job added: "%s"</info>', $job->getName()));
  }
}
