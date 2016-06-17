<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 11:10 PM
 */

namespace Frontend\Authentication;

use Frontend\Form\LoginForm;
use N3vrax\DkWebAuthentication\Action\LoginAction;
use N3vrax\DkWebAuthentication\Event\AuthenticationEvent;
use N3vrax\DkZendAuthentication\Adapter\CallbackCheck\DbCredentials;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class AuthenticationEventListener extends AbstractListenerAggregate
{
    /** @var  LoginForm */
    protected $loginForm;

    public function __construct(LoginForm $form)
    {
        $this->loginForm = $form;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEvents = $events->getSharedManager();

        //this will be called after the default prepare listener and the actual authentication call
        //use it for additional checks and data insertions into template
        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATE,
            [$this, 'preAuthentication'],
            25);

        //more listeners if you want to customize the flow...
    }

    /**
     * Pre authentication listener that happens before the default authentication
     * Can be used to prepare some data for the form and pre-validate data
     *
     * @param AuthenticationEvent $e
     */
    public function preAuthentication(AuthenticationEvent $e)
    {
        //we can leverage the default authentication flow
        //we will inject the form into the template, and validate data on POST
        //in case of errors, we set them on the event object
        $request = $e->getRequest();
        $form = $this->loginForm;
        $e->setParam('form', $form);

        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $form->setData($data);
            if(!$form->isValid()) {
                $e->addError('Invalid credential data. See errors below');
            }

            //insert a DbCredential object as request attribute as required by the auth adapter in use
            $identity = $form->getInputFilter()->getValue('identity');
            $credential = $form->getInputFilter()->getValue('credential');
            $dbCredentials = new DbCredentials($identity, $credential);

            $e->setRequest($request->withAttribute(DbCredentials::class, $dbCredentials));
        }
    }

    /**
     * @return LoginForm
     */
    public function getLoginForm()
    {
        return $this->loginForm;
    }

    /**
     * @param LoginForm $loginForm
     * @return AuthenticationEventListener
     */
    public function setLoginForm($loginForm)
    {
        $this->loginForm = $loginForm;
        return $this;
    }


}