<?php
/**
 * @file
 * Contains App\Handler\LoginPageHandler.
 */

namespace App\Handler;

use App\Entity\Profile;
use App\Service\AuthAlert;
use Auth0\SDK\Auth0;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

class LoginPageHandler extends BasePageHandler implements RequestHandlerInterface
{

    use AuthorizationTrait;

    protected Auth0 $client;

    public function __construct(Auth0 $auth0, ...$dependencies)
    {
        $this->client = $auth0;
        parent::__construct(...$dependencies);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();
        $login = $baseUrl . $this->router->generateUri('user.login');
        $params = $request->getQueryParams();
        if ($this->isAuthorized($request)) {
            return new RedirectResponse($this->router->generateUri('home'));
        } elseif (!empty($params['code']) && !empty($params['state'])) {
            $status = $this->client->exchange($login, $params['code'], $params['state']);
            if (!$status) {
                $this->alerts->addDanger(new AuthAlert("Auth error.", "Auth0 returned an error."));
            } else {
                $session = $this->client->getCredentials();
                ['email' => $email, 'nickname' => $nickname] = $session->user;
                $profile = $this->createProfileIfNotExists($email, $nickname, (array) $session);
                $this->authorize($request, $profile);
                return new RedirectResponse($login);
            }
        }

        return new HtmlResponse($this->template->render('app::login', [
            'login_redirect_url' => $this->client->login($login),
        ]));
    }

    protected function createProfileIfNotExists(string $email, string $nickname, array $token): Profile
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
