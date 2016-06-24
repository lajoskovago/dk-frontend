<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/21/2016
 * Time: 10:45 PM
 */

namespace N3vrax\DkUser\Listener;

use N3vrax\DkAuthentication\AuthenticationResult;
use N3vrax\DkBase\Session\FlashMessenger;
use N3vrax\DkWebAuthentication\Action\LoginAction;
use N3vrax\DkWebAuthentication\Event\AuthenticationEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Form;

class AuthenticationListener extends AbstractListenerAggregate
{
    /** @var  Form */
    protected $loginForm;

    /** @var  FlashMessenger */
    protected $flashMessenger;

    public function __construct(Form $form, FlashMessenger $flashMessenger)
    {
        $this->loginForm = $form;
        $this->flashMessenger = $flashMessenger;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEvents = $events->getSharedManager();

        //this will be called after the default prepare listener and the actual authentication call
        //use it for additional checks and data insertions into template
        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATE,
            [$this, 'injectData'],
            500);

        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATE,
            [$this, 'preAuthentication'],
            400);

        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATE,
            [$this, 'postAuthentication'],
            -50
        );
    }

    /**
     * We'll use this to inject our form into the event, consequently into the template
     *
     * @param AuthenticationEvent $e
     */
    public function injectData(AuthenticationEvent $e)
    {
        $form = $this->loginForm;
        $data = $this->flashMessenger->getData('loginFormData') ?: [];
        $messages = $this->flashMessenger->getData('loginFormMessages') ?: [];

        $form->setData($data);
        $form->setMessages($messages);

        $e->setParam('form', $form);
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
        $form = $e->getParam('form', null);

        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            if($form instanceof Form) {
                $form->setData($data);

                if(!$form->isValid()) {
                    foreach ($form->getMessages() as $message) {
                        $e->addError(current($message));
                    }

                    $this->flashMessenger->addData('loginFormData', $data);
                    $this->flashMessenger->addData('loginFormMessages', $form->getMessages());
                    return;
                }

                $data = array_merge($e->getParams(), $data, $form->getData());
                $e->setParams($data);
            }
        }
    }

    /**
     * In case authentication result is invalid, store form data and messages in session
     *
     * @param AuthenticationEvent $e
     */
    public function postAuthentication(AuthenticationEvent $e)
    {
        $request = $e->getRequest();
        /** @var Form $form */
        $form = $e->getParam('form');

        if($request->getMethod() === 'POST') {
            /** @var AuthenticationResult $result */
            $result = $e->getAuthenticationResult();
            if($result && !$result->isValid()) {
                $data = $form->getData();
                $this->flashMessenger->addData('loginFormData', $data);
            }
        }
    }

    /**
     * @return Form
     */
    public function getLoginForm()
    {
        return $this->loginForm;
    }

    /**
     * @param Form $loginForm
     * @return AuthenticationListener
     */
    public function setLoginForm($loginForm)
    {
        $this->loginForm = $loginForm;
        return $this;
    }
}