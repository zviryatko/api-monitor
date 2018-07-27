<?php
/**
 * @file
 * Contains App\Twig\Extensions\ActiveClass.
 */

namespace App\Twig\Extensions;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\ServiceManager\ServiceManager;

class ActiveClass extends \Twig_Extension
{
    /**
     * @var \Zend\Expressive\Router\RouterInterface
     */
    protected $router;

    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'active_class';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('active_class', [$this, 'activeClass']),
        ];
    }

    public function activeClass(ServerRequestInterface $request, $path, $class = ' active')
    {
        $route = $this->router->match($request);
        return $route->getMatchedRouteName() === (string) $path ? $class : '';
    }

    public static function factory(ServiceManager $serviceManager)
    {
        return new self($serviceManager->get(RouterInterface::class));
    }
}