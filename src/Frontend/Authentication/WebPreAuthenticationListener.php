<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/8/2016
 * Time: 6:49 PM
 */

namespace Frontend\Authentication;

use Frontend\Form\LoginForm;
use N3vrax\DkWebAuthentication\Event\WebAuthenticationEvent;
use N3vrax\DkWebAuthentication\Options\ModuleOptions;
use N3vrax\DkZendAuthentication\Adapter\CallbackCheck\DbCredentials;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class WebPreAuthenticationListener
{
    /** @var TemplateRendererInterface  */
    protected $template;

    /** @var LoginForm  */
    protected $loginForm;

    /** @var  UrlHelper */
    protected $urlHelper;

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
     * @param WebAuthenticationEvent $e
     * @return mixed
     */
    public function __invoke(WebAuthenticationEvent $e)
    {
        $request = $e->getRequest();

        $form = $this->loginForm;
        $this->template->addDefaultParam('app::login', 'form', $form);

        if($request->getMethod() === 'POST')
        {
            $data = $request->getParsedBody();
            $form->setData($data);

            $identity = '';
            $credential = '';
            if(is_array($data)) {
                $identity = isset($data['identity']) ? $data['identity'] : '';
                $credential = isset($data['credential']) ? $data['credential'] : '';
            }

            if(empty($identity) || empty($credential)) {
                $e->addError('Credentials are required and cannot be empty');
                return false;
            }

            $credentials = new DbCredentials($identity, $credential);
            $request = $request->withAttribute(DbCredentials::class, $credentials);
            $e->setRequest($request);
        }

        return true;
    }

}