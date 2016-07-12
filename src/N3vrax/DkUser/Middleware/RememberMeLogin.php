<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/12/2016
 * Time: 11:04 PM
 */

namespace N3vrax\DkUser\Middleware;

use N3vrax\DkAuthentication\AuthenticationInterface;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RememberMeLogin
{
    /** @var  UserOptions */
    protected $options;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  AuthenticationInterface */
    protected $authentication;

    public function  __construct(
        AuthenticationInterface $authentication,
        UserServiceInterface $userService,
        UserOptions $options
    )
    {
        $this->authentication = $authentication;
        $this->userService = $userService;
        $this->options = $options;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        if(!$this->authentication->hasIdentity()) {
            $cookies = $request->getCookieParams();
            $key = $this->options->getLoginOptions()->getRememberMeCookieName();

            if(isset($cookies[$key])) {
                $data = unserialize($cookies[$key]);

                $selector = $data['selector'];
                $token = $data['token'];

                $r = $this->userService->checkRememberToken($selector, $token);
                if($r) {
                    $userId = $r['userId'];
                    $user = $this->userService->findUser($userId);

                    if($user) {
                        //renew the tokens
                        $this->userService->removeRememberToken($userId);
                        $this->userService->generateRememberToken($userId);
                        
                        //autologin user
                        $this->authentication->setIdentity($user);
                    }
                }
            }
        }

        return $next($request, $response);
    }
}