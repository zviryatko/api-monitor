<?php
/**
 * @file
 * Contains App\Handler\LoginPageHandler.
 */

namespace App\Handler;

use App\Entity\Profile;
use App\Service\AuthAlert;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

class LoginPageHandler extends BasePageHandler implements RequestHandlerInterface
{

    use AuthorizationTrait;

    protected $client;

    public function __construct(\Google_Client $googleService, ...$dependencies)
    {
        $this->client = $googleService;
        parent::__construct(...$dependencies);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->isAuthorized($request)) {
            return new RedirectResponse($this->router->generateUri('home'));
        } elseif (!empty($request->getQueryParams()['code'])) {
            $code = $request->getQueryParams()['code'];
            $token = $this->client->authenticate($code);
            if (!empty($token['error'])) {
                $this->alerts->addDanger(new AuthAlert($token['error'], $token['error_description']));
            } else {
                $oauthService = new \Google_Service_Oauth2($this->client);
                $data = $oauthService->userinfo->get();
                ['email' => $email, 'givenName' => $nickname] = $data;
                $profile = $this->createProfileIfNotExists($email, $nickname, $token);
                $this->authorize($request, $profile);
                return new RedirectResponse($this->router->generateUri('home'));
            }
        }

        return new HtmlResponse($this->template->render('app::login', [
            'login_redirect_url' => $this->client->createAuthUrl([\Google_Service_Oauth2::USERINFO_EMAIL]),
        ]));
    }

    protected function createProfileIfNotExists($email, $nickname, $token): Profile
    {
        $repo = $this->storage->getRepository(Profile::class);
        $profile = $repo->findOneBy(['mail' => $email]);
        if (!$profile) {
            $profile = new Profile($nickname, $email, $token);
            $this->storage->persist($profile);
            $this->storage->flush();
        }
        return $profile;
    }
}
