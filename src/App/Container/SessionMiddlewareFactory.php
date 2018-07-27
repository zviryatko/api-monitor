<?php
/**
 * @file
 * Contains App\Container\SessionMiddlewareFactory.
 */

namespace App\Container;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class SessionMiddlewareFactory implements FactoryInterface
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
        if (empty($container->get('config')['session_key'])) {
            throw new ServiceNotCreatedException("Provide session_key configuration option.");
        }
        $key = $container->get('config')['session_key'];
        $expirationTime = 60 * 60 * 24 * 7;
        return SessionMiddleware::fromSymmetricKeyDefaults($key, $expirationTime);
    }
}