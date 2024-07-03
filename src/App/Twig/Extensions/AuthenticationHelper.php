<?php
/**
 * @file
 * Contains App\Twig\Extensions\AuthenticationHelper.
 */

namespace App\Twig\Extensions;

use App\Entity\Profile;
use App\Helper\AuthenticationMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Session\SessionMiddleware;

class AuthenticationHelper extends AbstractExtension
{

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'auth_helper';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('is_authorized', [$this, 'isAuthorized']),
            new TwigFunction('username', [$this, 'username']),
            new TwigFunction('csrf', [$this, 'csrf']),
        ];
    }

    public function isAuthorized(ServerRequestInterface $request): bool
    {
        $profile = $request->getAttribute(AuthenticationMiddleware::PROFILE_ATTRIBUTE);
        return $profile instanceof Profile;
    }

    public function username(ServerRequestInterface $request): string
    {
        $profile = $request->getAttribute(AuthenticationMiddleware::PROFILE_ATTRIBUTE);
        if (!$profile instanceof Profile) {
            return '';
        }
        return $profile->nickname();
    }

    public function csrf(ServerRequestInterface $request): string
    {
        /** @var \Mezzio\Session\SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if ($session->has('__csrf')) {
            return $session->get('__csrf');
        }
        /** @var \Mezzio\Csrf\CsrfGuardInterface $guard */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        return $guard->generateToken();
    }
}
