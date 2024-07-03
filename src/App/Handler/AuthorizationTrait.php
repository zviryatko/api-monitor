<?php
/**
 * @file
 * Contains AuthorizationTrait
 */

namespace App\Handler;


use App\Entity\Profile;
use App\Helper\AuthenticationMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Session\SessionMiddleware;

trait AuthorizationTrait
{
    protected function isAuthorized(ServerRequestInterface $request): bool
    {
        $profile = $request->getAttribute(AuthenticationMiddleware::PROFILE_ATTRIBUTE);
        return $profile instanceof Profile;
    }

    protected function authorize(ServerRequestInterface $request, Profile $profile): void
    {
        /** @var \Mezzio\Session\SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $session->set(AuthenticationMiddleware::USER_ID_SESSION_KEY, $profile->id());
    }

    protected function forget(ServerRequestInterface $request): void
    {
        /** @var \Mezzio\Session\SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $session->clear();
    }

    protected function getProfile(ServerRequestInterface $request): ?Profile
    {
        return $request->getAttribute(AuthenticationMiddleware::PROFILE_ATTRIBUTE);
    }
}
