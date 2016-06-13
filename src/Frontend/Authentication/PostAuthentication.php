<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/13/2016
 * Time: 5:52 PM
 */

namespace Frontend\Authentication;

use N3vrax\DkAuthentication\AuthenticationResult;
use N3vrax\DkWebAuthentication\PostAuthenticationStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostAuthentication implements PostAuthenticationStrategy
{
    /** @var  ServerRequestInterface */
    protected $request;

    /** @var  ResponseInterface */
    protected $response;

    /** @var  AuthenticationResult */
    protected $result;

    public function postAuthentication(
        ServerRequestInterface $request,
        ResponseInterface $response,
        AuthenticationResult $result)
    {
        $this->request = $request;
        $this->response = $response;
        $this->result = $result;

        //clear the session if authentication was successful, see pre auth for matching logic
        $session = $request->getAttribute('session', null);
        if($result->isValid() && $session && isset($session->loginData)) {
            unset($session->loginData);
        }
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

}