<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/8/2016
 * Time: 6:49 PM
 */

namespace Frontend\Authentication;

use Frontend\Form\LoginForm;
use N3vrax\DkWebAuthentication\LoginAction;
use N3vrax\DkWebAuthentication\PreAuthCallbackResult;
use N3vrax\DkZendAuthentication\Adapter\CallbackCheck\DbCredentials;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class PreAuthCallback
{
    /** @var TemplateRendererInterface  */
    protected $template;

    /** @var LoginForm  */
    protected $loginForm;

    /** @var  UrlHelper */
    protected $urlHelper;

    /**
     * PreAuthCallback constructor.
     * @param TemplateRendererInterface $template
     * @param LoginForm $loginForm
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        TemplateRendererInterface $template,
        LoginForm $loginForm,
        UrlHelper $urlHelper)
    {
        $this->template = $template;
        $this->loginForm = $loginForm;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return PreAuthCallbackResult|HtmlResponse
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $form = $this->loginForm;

        $session = $request->getAttribute('session', null);
        if($session && isset($session->loginData)) {
            $data = $session->loginData;
            if(isset($data['credential']))
                unset($data['credential']);

            $form->setData($data);
        }

        /** @var ServerRequestInterface $request */
        $request = $request->withAttribute(LoginAction::LOGIN_TEMPLATE_DATA, ['form' => $form]);

        if($request->getMethod() === 'POST')
        {
            $data = $request->getParsedBody();
            $form->setData($data);
            /*if(!$form->isValid())
            {

            }*/
            $identity = '';
            $credential = '';
            if(is_array($data)) {
                $identity = isset($data['identity']) ? $data['identity'] : '';
                $credential = isset($data['credential']) ? $data['credential'] : '';
            }

            if(empty($identity) || empty($credential)) {

                if(isset($data['credential']))
                    unset($data['credential']);

                if($session) {
                    $session->loginData = $data;
                }

                return new PreAuthCallbackResult(
                    $request,
                    $response,
                    ['Credentials are required and cannot be empty']);
            }

            $credentials = new DbCredentials($identity, $credential);
            $request = $request->withAttribute(DbCredentials::class, $credentials);
        }

        return new PreAuthCallbackResult($request, $response);
    }
}