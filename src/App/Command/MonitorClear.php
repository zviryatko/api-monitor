<?php

namespace App\Command;

use App\Entity\Job;
use App\Entity\Log;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorClear extends CommandBase
{
    public const NAME = 'monitor:clear';

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Remove logs older than 30 days');
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $storage = $this->storage;
        $storage->createQueryBuilder()
            ->delete(Log::class, 'l')
            ->andWhere('l.created < :date')
            ->setParameter('date', new \DateTime('-30 days'))
            ->getQuery()
            ->execute();
        return Command::SUCCESS;
    }
}
