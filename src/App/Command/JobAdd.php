<?php

namespace App\Command;

use App\Entity\Job;
use App\Entity\Profile;
use App\Entity\Project;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobAdd extends CommandBase
{

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName('job:add')
            ->setDescription('Add job')
            ->addArgument(
                'profile',
                InputArgument::REQUIRED,
                'Profile mail or id'
            )
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $profileNameOrId = $input->getArgument('profile');
        $prop = is_numeric($profileNameOrId) ? 'id' : 'mail';
        $profile = $this->storage->getRepository(Profile::class)->findOneBy([$prop => $profileNameOrId]);
        if (!$profile instanceof Profile) {
            $output->writeln(sprintf('<error>Profile with %s "%s" is not found.</error>', $prop, $profileNameOrId));
            return;
        }
        $name = $input->getArgument('name');
        $command = $input->getArgument('exec');
        $project = $this->getDefaultProject($profile);
        $job = new Job($name, $profile, $project, $command);
        $this->storage->persist($job);
        $this->storage->flush();
        $output->writeln(sprintf('<info>Job added: "%s"</info>', $job->getName()));
    }


    private function getDefaultProject(Profile $profile): Project
    {
        $project = $this->storage->getRepository(Project::class)->findOneBy(['owner' => $profile->id()]);
        if (!$project) {
            $project = new Project(
                sprintf('%s\'s default', $profile->nickname()),
                $profile,
                sprintf('%s-default', $profile->nickname()),
                false
            );
        }

        return $project;
    }
}
