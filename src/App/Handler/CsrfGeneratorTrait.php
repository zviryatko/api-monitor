<?php

namespace App\Handler;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Session\SessionMiddleware;

trait CsrfGeneratorTrait
{
    protected function getCsrfToken(ServerRequestInterface $request)
    {
        /** @var \Zend\Expressive\Csrf\SessionCsrfGuard $guard */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        /** @var \Zend\Expressive\Session\LazySession $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if (!$session->has('__csrf')) {
            $guard->generateToken('__csrf');
        }
        return $session->get('__csrf');
    }
}
