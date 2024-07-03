<?php
/**
 * @file
 * Contains App\Handler\UserPageHandler.
 */

namespace App\Handler;

use App\Entity\Profile;
use App\Handler\BasePageHandler;
use App\Helper\AuthenticationMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

class UserPageHandler extends BasePageHandler implements RequestHandlerInterface
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $profile = $request->getAttribute(AuthenticationMiddleware::PROFILE_ATTRIBUTE);
        if (!$profile instanceof Profile) {
            return new RedirectResponse($this->router->generateUri('user.login'));
        }
        return new HtmlResponse($this->template->render("app::user", ['profile' => $profile]));
    }
}
