<?php
declare(strict_types=1);

namespace App\Handler;

use App\Service\AlertsInterface;
use Doctrine\ORM\EntityManagerInterface;
use Mezzio\Router;
use Mezzio\Template;

abstract class BasePageHandler
{
    protected $router;

    protected $template;

    protected $storage;

    protected $alerts;

    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template,
        EntityManagerInterface $storage,
        AlertsInterface $alerts
    ) {
        $this->router = $router;
        $this->template = $template;
        $this->storage = $storage;
        $this->alerts = $alerts;
    }
}
