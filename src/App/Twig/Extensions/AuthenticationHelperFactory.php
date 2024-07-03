<?php

namespace App\Twig\Extensions;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Mezzio\Helper\UrlHelper;

class AuthenticationHelperFactory implements FactoryInterface
{
    public function __invoke($container, $requestedName, array $options = null)
    {
        return new AuthenticationHelper(
            $container->get(UrlHelper::class)
        );
    }
}
