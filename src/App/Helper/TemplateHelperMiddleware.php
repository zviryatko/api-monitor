<?php
/**
 * @file
 * Contains App\Helper\TwigHelperMiddleware.
 */

namespace App\Helper;

use App\Service\AlertsInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TemplateHelperMiddleware implements MiddlewareInterface
{
    /**
     * @var \Mezzio\Template\TemplateRendererInterface
     */
    protected $renderer;

    /**
     * @var \App\Service\AlertsInterface
     */
    protected $alerts;

    public function __construct(TemplateRendererInterface $renderer, AlertsInterface $alerts)
    {
        $this->renderer = $renderer;
        $this->alerts = $alerts;
    }

    /**
     * Inject the ServerRequestInterface instance to twig layout.
     *
     * Injects the ServerUrlHelper with the incoming request URI, and then invoke
     * the next middleware.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'request', $request);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'errors', $this->alerts);
        return $handler->handle($request);
    }
}
