<?php
/**
 * @file
 * Contains App\Service\GoogleClientFactory.
 */

namespace App\Service;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;

class GoogleClientFactory
{

    public function __invoke(ContainerInterface $container)
    {
        /** @var RouterInterface $router */
        $router = $container->get(RouterInterface::class);
        /** @var ServerRequestInterface $request */
        $request = $container->get(ServerRequestInterface::class)();
        $serverParams = $request->getServerParams();
        $redirectUri = implode([
            empty($serverParams['HTTPS']) ? 'http://' : 'https://',
            $serverParams['HTTP_HOST'],
            $router->generateUri('user.login'),
        ]);
        /** @var array $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $client = new \Google_Client([
            'client_id' => $config['google']['google_client_id'],
            'client_secret' => $config['google']['google_client_secret'],
            'redirect_uri' => $redirectUri,
        ]);
        $client->addScope(\Google_Service_Oauth2::USERINFO_EMAIL);
        return $client;
    }
}