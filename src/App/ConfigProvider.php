<?php

declare(strict_types=1);

namespace App;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'console' => $this->getConsole(),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
            ],
            'factories' => [
                Handler\HomePageHandler::class => Handler\PageHandlerFactory::class,
                Handler\JobListHandler::class => Handler\PageHandlerFactory::class,
                Handler\JobAddHandler::class => Handler\PageHandlerFactory::class,
                Handler\JobEditHandler::class => Handler\PageHandlerFactory::class,
                Command\Monitor::class => Command\CommandFactory::class,
                Command\JobAdd::class => Command\CommandFactory::class,
                Command\JobRemove::class => Command\CommandFactory::class,
                Command\JobExec::class => Command\CommandFactory::class,
                Command\JobList::class => Command\CommandFactory::class,
                Command\MonitorRunAll::class => Command\CommandFactory::class,
            ],
        ];
    }

    public function getConsole(): array
    {
        return [
            'commands' => [
                Command\Monitor::class,
                Command\MonitorRunAll::class,
                Command\JobAdd::class,
                Command\JobRemove::class,
                Command\JobExec::class,
                Command\JobList::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app' => ['templates/app'],
                'error' => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
        ];
    }
}
