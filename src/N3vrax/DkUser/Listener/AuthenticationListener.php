<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/21/2016
 * Time: 10:45 PM
 */

namespace N3vrax\DkUser\Listener;

use N3vrax\DkWebAuthentication\Action\LoginAction;
use N3vrax\DkWebAuthentication\Event\AuthenticationEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Form;

class AuthenticationListener extends AbstractListenerAggregate
{
    /** @var  Form */
    protected $loginForm;

    public function __construct(Form $form)
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
            [$this, 'injectData'],
            500);

        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATE,
            [$this, 'preAuthentication'],
            400);
    }

    public function injectData(AuthenticationEvent $e)
    {
        $form = $this->loginForm;
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
                    $e->addError('Invalid or missing credentials. Please try again');
                    return;
                }

                $data = array_merge($e->getParams(), $data, $form->getInputFilter()->getValues());
                $e->setParams($data);
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