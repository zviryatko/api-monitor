<?php

namespace App\Command;

use App\Entity\Job;
use App\Entity\Log;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Monitor extends CommandBase {

  /**
   * Configures the command
   */
  protected function configure() {
    $this
      ->setName('monitor:log')
      ->setDescription('Log service')
      ->addArgument(
        'name_or_id',
        InputArgument::REQUIRED,
        'Job name or id'
      );
  }

  /**
   * Executes the current command
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $name_or_id = $input->getArgument('name_or_id');
    $prop = is_numeric($name_or_id) ? 'id' : 'name';
    $job = $this->storage->getRepository(Job::class)->findOneBy([$prop => $name_or_id]);
    if ($job instanceof Job) {
      $log = new Log($job, $job->execute());
      $this->storage->persist($log);
      $this->storage->flush();
      $output->writeln(sprintf('<info>Added log "%s", %s</info>', $job->getName(), $log->getCreated()
        ->format('d/m/Y H:i:s')));
    }
    else {
      $output->writeln(sprintf('<error>Job with %s "%s" is not found.</error>', $prop, $name_or_id));
    }
  }
}
