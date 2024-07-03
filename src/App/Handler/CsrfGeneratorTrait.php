<?php

namespace App\Handler;

use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Session\SessionMiddleware;

trait CsrfGeneratorTrait
{
    protected function getCsrfToken(ServerRequestInterface $request)
    {
        /** @var \Mezzio\Csrf\SessionCsrfGuard $guard */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        /** @var \Mezzio\Session\LazySession $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if (!$session->has('__csrf')) {
            $guard->generateToken('__csrf');
        }
        return $session->get('__csrf');
    }
}
