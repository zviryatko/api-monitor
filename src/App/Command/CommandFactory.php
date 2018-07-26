<?php
/**
 * @file
 * Contains JobFactory
 */

namespace App\Command;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class CommandFactory implements FactoryInterface {

  /**
   * Create an object
   *
   * @param  ContainerInterface $container
   * @param  string $requestedName
   * @param  null|array $options
   * @return object
   * @throws ServiceNotFoundException if unable to resolve the service.
   * @throws ServiceNotCreatedException if an exception is raised when
   *     creating a service.
   * @throws ContainerException if any other error occurs
   */
  public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
    if (class_exists($requestedName)) {
      return new $requestedName($container->get(EntityManager::class));
    }
    throw new InvalidServiceException(sprintf('Container with name "%s" not found.', $requestedName));
  }
}
