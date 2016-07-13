<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/12/2016
 * Time: 11:04 PM
 */

namespace N3vrax\DkUser\Middleware;

use N3vrax\DkAuthentication\AuthenticationInterface;
use N3vrax\DkBase\Session\FlashMessenger;
use N3vrax\DkUser\Options\LoginOptions;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class AutoLogin
{
    /** @var  UserOptions */
    protected $options;

    /** @var  FlashMessenger */
    protected $flashMessenger;

    /** @var  UrlHelper */
    protected $urlHelper;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  AuthenticationInterface */
    protected $authentication;

    /** @var  ServerRequestInterface */
    protected $request;

    public function  __construct(
        AuthenticationInterface $authentication,
        UserServiceInterface $userService,
        UrlHelper $urlHelper,
        FlashMessenger $messenger,
        UserOptions $options
    )
    {
        $this->authentication = $authentication;
        $this->userService = $userService;
        $this->urlHelper = $urlHelper;
        $this->flashMessenger = $messenger;
        $this->options = $options;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $this->request = $request;

        if(!$this->authentication->hasIdentity()) {
            $cookies = $request->getCookieParams();
            $key = $this->options->getLoginOptions()->getRememberMeCookieName();

            if(isset($cookies[$key])) {
                try{
                    $data = @unserialize(base64_decode($cookies[$key]));

                    if($data) {
                        $selector = $data['selector'];
                        $token = $data['token'];

                        $r = $this->userService->checkRememberToken($selector, $token);
                        if($r) {
                            $userId = (int) $r['userId'];
                            $user = $this->userService->findUser($userId);

                            if($user) {
                                //renew the tokens
                                $this->userService->removeRememberToken($user);
                                $this->userService->generateRememberToken($user);

                                //autologin user
                                $this->authentication->setIdentity($user);
                            }
                        }
                        else {
                            return $this->redirectToLoginWithError($this->options->getLoginOptions()
                                ->getMessage(LoginOptions::MESSAGE_AUTO_LOGIN_ERROR));
                        }
                    }
                    else {
                        return $this->redirectToLoginWithError($this->options->getLoginOptions()
                            ->getMessage(LoginOptions::MESSAGE_AUTO_LOGIN_ERROR));
                    }
                }
                catch(\Exception $e) {
                    error_log("Auto-login error: " . $e->getMessage());
                    return $this->redirectToLoginWithError($this->options->getLoginOptions()
                        ->getMessage(LoginOptions::MESSAGE_AUTO_LOGIN_ERROR));
                }
            }
        }

        return $next($request, $response);
    }

    protected function redirectToLoginWithError($message)
    {
        $this->unsetRememberCookie($this->options->getLoginOptions()->getRememberMeCookieName());

        $this->flashMessenger->addError($message);

        $loginUri = $this->urlHelper->generate('login');
        $loginUri .= '?' . http_build_query(['redirect' => $this->request->getUri()->__toString()]);
        return new RedirectResponse($loginUri);
    }

    protected function unsetRememberCookie($key)
    {
        if(isset($_COOKIE[$key])) {
            unset($_COOKIE[$key]);
            setcookie($key, '', time() - 3600, '/');
        }
    }
}