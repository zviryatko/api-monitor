<?php
/**
 * @file
 * Contains App\Handler\LogoutPageHandler.
 */

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Csrf\CsrfMiddleware;

class LogoutPageHandler extends BasePageHandler implements RequestHandlerInterface
{

    use AuthorizationTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var \Zend\Expressive\Csrf\CsrfGuardInterface $guard */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        if ($guard->validateToken($request->getAttribute('token'))) {
            $this->forget($request);
        }
        return new RedirectResponse($this->router->generateUri('home'));
    }
}
