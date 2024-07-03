<?php
/**
 * @file
 * Contains App\Twig\Extensions\AuthenticationHelper.
 */

namespace App\Twig\Extensions;

use App\Entity\Profile;
use App\Helper\AuthenticationMiddleware;
use Mezzio\Helper\UrlHelper;
use Mezzio\Helper\UrlHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Session\SessionMiddleware;

class AuthenticationHelper extends AbstractExtension
{
    private UrlHelperInterface $urlHelper;

    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

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
            new TwigFunction('picture', [$this, 'picture']),
            new TwigFunction('csrf', [$this, 'csrf']),
        ];
    }

    public function isAuthorized(): bool
    {
        $request = $this->urlHelper->getRequest();
        $profile = $request->getAttribute(AuthenticationMiddleware::PROFILE_ATTRIBUTE);
        return $profile instanceof Profile;
    }

    public function username(): string
    {
        $request = $this->urlHelper->getRequest();
        $profile = $request->getAttribute(AuthenticationMiddleware::PROFILE_ATTRIBUTE);
        if (!$profile instanceof Profile) {
            return '';
        }
        return $profile->token()['user']['name'] ?? $profile->nickname();
    }

    public function picture(): string
    {
        $request = $this->urlHelper->getRequest();
        $profile = $request->getAttribute(AuthenticationMiddleware::PROFILE_ATTRIBUTE);
        if (!$profile instanceof Profile) {
            return '';
        }
        return $profile->token()['user']['picture'] ?? '';
    }

    public function csrf(): string
    {
        $request = $this->urlHelper->getRequest();
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
