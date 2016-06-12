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
use Zend\Expressive\Template\TemplateRendererInterface;

class PreAuthCallback
{
    /** @var TemplateRendererInterface  */
    protected $template;

    /** @var LoginForm  */
    protected $loginForm;

    /**
     * PreAuthCallback constructor.
     * @param TemplateRendererInterface $template
     * @param LoginForm $loginForm
     */
    public function __construct(TemplateRendererInterface $template, LoginForm $loginForm)
    {
        $this->template = $template;
        $this->loginForm = $loginForm;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return PreAuthCallbackResult|HtmlResponse
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        //add some extra data to the template
        $form = $this->loginForm;

        if($request->getMethod() === 'POST')
        {
            $data = $request->getParsedBody();
            $identity = '';
            $credential = '';
            if(is_array($data)) {
                $identity = isset($data['identity']) ? $data['identity'] : '';
                $credential = isset($data['credential']) ? $data['credential'] : '';
            }

            if(empty($identity) || empty($credential)) {
                return new HtmlResponse($this->template->render('app::login',
                    ['messages' => ['Authentication failure. Credentials are required and cannot be empty'],
                        'identity' => $identity]));
            }

            $credentials = new DbCredentials($identity, $credential);
            $request = $request->withAttribute(DbCredentials::class, $credentials);
        }

        //add the form to the requestm so that the login action would inject it into the template
        /** @var ServerRequestInterface $request */
        $request = $request->withAttribute(LoginAction::LOGIN_TEMPLATE_DATA, ['form' => $form]);

        return new PreAuthCallbackResult($request, $response);
    }
}