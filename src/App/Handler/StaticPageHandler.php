<?php
/**
 * @file
 * Contains App\Handler\HomePageAction.
 */

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class StaticPageHandler extends BasePageHandler implements RequestHandlerInterface
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeName = $this->router->match($request)->getMatchedRouteName();
        return new HtmlResponse($this->template->render("app::static::{$routeName}"));
    }
}