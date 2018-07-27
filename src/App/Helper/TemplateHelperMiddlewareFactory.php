<?php
/**
 * @file
 * Contains App\Helper\TwigHelperMiddlewareFactory.
 */

namespace App\Helper;

use App\Service\AlertsInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TemplateHelperMiddlewareFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new TemplateHelperMiddleware(
            $container->get(TemplateRendererInterface::class),
            $container->get(AlertsInterface::class)
        );
    }
}