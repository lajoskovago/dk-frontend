<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/21/2016
 * Time: 10:55 PM
 */

namespace N3vrax\DkUser\Middleware;

use N3vrax\DkUser\Listener\AuthenticationListener;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\EventManager\EventManagerAwareTrait;

class Bootstrap
{
    use EventManagerAwareTrait;

    /** @var  AuthenticationListener */
    protected $authenticationListener;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        if($this->authenticationListener) {
            $this->authenticationListener->attach($this->getEventManager());
        }

        return $next($request, $response);
    }

    /**
     * @return AuthenticationListener
     */
    public function getAuthenticationListener()
    {
        return $this->authenticationListener;
    }

    /**
     * @param AuthenticationListener $authenticationListener
     * @return Bootstrap
     */
    public function setAuthenticationListener($authenticationListener)
    {
        $this->authenticationListener = $authenticationListener;
        return $this;
    }
}