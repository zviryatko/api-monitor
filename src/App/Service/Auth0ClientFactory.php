<?php

namespace App\Service;

use Auth0\SDK\Auth0;
use Mezzio\Router\RouterInterface;

class Auth0ClientFactory
{
    public function __invoke($container): Auth0
    {
        $config = $container->get('config');
        $auth0Config = $config['auth0'];
        $auth0Config['redirectUri'] = $container->get(RouterInterface::class)->generateUri('user.login');
//        $auth0Config['store'] = $container->get('session');
        return new Auth0($auth0Config);
    }
}
