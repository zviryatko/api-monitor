<?php
/**
 * @file
 * Contains App\Twig\Extensions\ActiveClass.
 */

namespace App\Twig\Extensions;

use Psr\Http\Message\ServerRequestInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Mezzio\Router\RouterInterface;
use Laminas\ServiceManager\ServiceManager;

class ActiveClass extends AbstractExtension
{
    /**
     * @var \Mezzio\Router\RouterInterface
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
            new TwigFunction('active_class', [$this, 'activeClass']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('truncate', [$this, 'truncate']),
        ];
    }

    public function truncate(string $text, int $length = 100, string $ending = '...'): string
    {
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length - strlen($ending)) . $ending;
        }
        return $text;
    }

    public function activeClass(ServerRequestInterface $request, $path, $class = ' active'): string
    {
        $route = $this->router->match($request);
        return $route->getMatchedRouteName() === (string) $path ? $class : '';
    }

    public static function factory(ServiceManager $serviceManager)
    {
        return new self($serviceManager->get(RouterInterface::class));
    }
}
