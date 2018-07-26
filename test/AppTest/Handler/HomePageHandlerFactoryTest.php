<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\HomePageHandler;
use App\Handler\PageHandlerFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageHandlerFactoryTest extends TestCase
{
    /** @var ContainerInterface|ObjectProphecy */
    protected $container;

    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $router = $this->prophesize(RouterInterface::class);

        $this->container->get(RouterInterface::class)->willReturn($router);
    }

    public function testFactoryWithoutTemplate()
    {
        $factory = new PageHandlerFactory();
        $this->container->has(TemplateRendererInterface::class)->willReturn(false);

        $this->assertInstanceOf(PageHandlerFactory::class, $factory);

        $homePage = $factory($this->container->reveal(), null, get_class($this->container->reveal()));

        $this->assertInstanceOf(HomePageHandler::class, $homePage);
    }

    public function testFactoryWithTemplate()
    {
        $this->container->has(TemplateRendererInterface::class)->willReturn(true);
        $this->container
            ->get(TemplateRendererInterface::class)
            ->willReturn($this->prophesize(TemplateRendererInterface::class));

        $factory = new PageHandlerFactory();

        $homePage = $factory($this->container->reveal(), null, get_class($this->container->reveal()));

        $this->assertInstanceOf(HomePageHandler::class, $homePage);
    }
}
