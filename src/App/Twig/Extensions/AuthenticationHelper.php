<?php
/**
 * @file
 * Contains App\Twig\Extensions\AuthenticationHelper.
 */

namespace App\Twig\Extensions;

use App\Entity\Profile;
use App\Helper\AuthenticationMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Session\SessionMiddleware;

class AuthenticationHelper extends \Twig_Extension
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
            new \Twig_SimpleFunction('is_authorized', [$this, 'isAuthorized']),
            new \Twig_SimpleFunction('username', [$this, 'username']),
            new \Twig_SimpleFunction('csrf', [$this, 'csrf']),
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
        /** @var \Zend\Expressive\Session\SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if ($session->has('__csrf')) {
            return $session->get('__csrf');
        }
        /** @var \Zend\Expressive\Csrf\CsrfGuardInterface $guard */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        return $guard->generateToken();
    }
}