<?php
declare(strict_types=1);

namespace App\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Zend\Expressive\Router;
use Zend\Expressive\Template;

class BasePageHandler
{
    protected $router;

    protected $template;

    protected $storage;

    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template,
        EntityManagerInterface $storage
    ) {
        $this->router = $router;
        $this->template = $template;
        $this->storage = $storage;
    }
}
