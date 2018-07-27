<?php
/**
 * @file
 * Contains App\Handler\LoginPageHandlerFactory.
 */

namespace App\Handler;


use App\Service\AlertsInterface;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class LoginPageHandlerFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new LoginPageHandler(
            $container->get(\Google_Client::class),
            $container->get(RouterInterface::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(EntityManager::class),
            $container->get(AlertsInterface::class)
        );
    }
}