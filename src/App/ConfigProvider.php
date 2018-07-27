<?php

declare(strict_types=1);

namespace App;

use App\Service\GoogleClientFactory;

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
            'doctrine' => $this->getDoctrine(),
            'twig' => $this->getTwig(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Service\AlertsInterface::class => Service\Alerts::class,
                Twig\Extensions\AuthenticationHelper::class => Twig\Extensions\AuthenticationHelper::class,
            ],
            'factories' => [
                Handler\HomePageHandler::class => Handler\PageHandlerFactory::class,
                Handler\JobListHandler::class => Handler\PageHandlerFactory::class,
                Handler\JobFormHandler::class => Handler\PageHandlerFactory::class,
                Handler\LogoutPageHandler::class => Handler\PageHandlerFactory::class,
                Handler\LoginPageHandler::class => Handler\LoginPageHandlerFactory::class,
                Handler\RegisterPageHandler::class => Handler\PageHandlerFactory::class,
                Handler\StaticPageHandler::class => Handler\PageHandlerFactory::class,
                Handler\UserPageHandler::class => Handler\PageHandlerFactory::class,
                Command\Monitor::class => Command\CommandFactory::class,
                Command\JobAdd::class => Command\CommandFactory::class,
                Command\JobRemove::class => Command\CommandFactory::class,
                Command\JobExec::class => Command\CommandFactory::class,
                Command\JobList::class => Command\CommandFactory::class,
                Command\MonitorRunAll::class => Command\CommandFactory::class,
                Helper\TemplateHelperMiddleware::class => Helper\TemplateHelperMiddlewareFactory::class,
                Helper\AuthenticationMiddleware::class => [Helper\AuthenticationMiddleware::class, 'factory'],
                Twig\Extensions\ActiveClass::class => [Twig\Extensions\ActiveClass::class, 'factory'],
                Twig\Extensions\ElementError::class => [Twig\Extensions\ElementError::class, 'factory'],
                \Zend\Mail\Transport\TransportInterface::class => Container\MailTransportFactory::class,
                \Google_Client::class => GoogleClientFactory::class
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

    public function getDoctrine()
    {
        return [
            'driver' => [
                'orm_default' => [
                    'class' => \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain::class,
                    'drivers' => [
                        'App\Entity' => 'app_entity',
                    ],
                ],
                'app_entity' => [
                    'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => __DIR__ . '/Entity',
                ],
            ],
        ];
    }

    public function getTwig()
    {
        return [
            'extensions' => [
                \App\Twig\Extensions\ActiveClass::class,
                \App\Twig\Extensions\ElementError::class,
                \App\Twig\Extensions\AuthenticationHelper::class,
            ],
        ];
    }
}
