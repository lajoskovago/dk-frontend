<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/8/2016
 * Time: 6:49 PM
 */

namespace Frontend\Authentication;

use Frontend\Form\LoginForm;
use N3vrax\DkWebAuthentication\Options\ModuleOptions;
use N3vrax\DkWebAuthentication\PreAuthenticationStrategy;
use N3vrax\DkZendAuthentication\Adapter\CallbackCheck\DbCredentials;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class PreAuthentication implements PreAuthenticationStrategy
{
    /** @var TemplateRendererInterface  */
    protected $template;

    /** @var LoginForm  */
    protected $loginForm;

    /** @var  UrlHelper */
    protected $urlHelper;

    /** @var  ServerRequestInterface */
    protected $request;

    /** @var  ResponseInterface */
    protected $response;

    /** @var  string[] */
    protected $errors = [];

    /** @var  ModuleOptions */
    protected $webAuthOptions;

    /**
     * PreAuthCallback constructor.
     * @param TemplateRendererInterface $template
     * @param LoginForm $loginForm
     * @param UrlHelper $urlHelper
     * @param ModuleOptions $webAuthOptions
     */
    public function __construct(
        TemplateRendererInterface $template,
        LoginForm $loginForm,
        UrlHelper $urlHelper,
        ModuleOptions $webAuthOptions)
    {
        $this->template = $template;
        $this->loginForm = $loginForm;
        $this->urlHelper = $urlHelper;
        $this->webAuthOptions = $webAuthOptions;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function preAuthentication(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;

        $data = [];
        $session = $request->getAttribute('session', null);
        if($session && isset($session->loginData)) {
            $data = $session->loginData;
            if(isset($data['credential'])) {
                unset($data['credential']);
            }

            unset($session->loginData);
        }

        $form = $this->loginForm;
        $form->setData($data);

        if($request->getMethod() === 'POST')
        {
            $data = $request->getParsedBody();
            //set login data in session to repopulate the form if errors
            //in case authentication succeeds, we clear the session in the post authentication call
            if($session) {
                $session->loginData = $data;
            }
            $form->setData($data);

            $identity = '';
            $credential = '';
            if(is_array($data)) {
                $identity = isset($data['identity']) ? $data['identity'] : '';
                $credential = isset($data['credential']) ? $data['credential'] : '';
            }

            if(empty($identity) || empty($credential)) {
                $this->errors[] = 'Credentials are required and cannot be empty';
                $this->template->addDefaultParam('app::login', 'form', $form);

                return false;
            }

            $credentials = new DbCredentials($identity, $credential);
            $this->request = $this->request->withAttribute(DbCredentials::class, $credentials);
        }

        $this->template->addDefaultParam('app::login', 'form', $form);
        return true;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function isValid()
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }


}