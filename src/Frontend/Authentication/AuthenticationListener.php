<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 11:10 PM
 */

namespace Frontend\Authentication;

use N3vrax\DkWebAuthentication\Action\LoginAction;
use N3vrax\DkWebAuthentication\Event\AuthenticationEvent;
use N3vrax\DkZendAuthentication\Adapter\CallbackCheck\DbCredentials;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class AuthenticationListener extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEvents = $events->getSharedManager();

        //this will be called after the default prepare listener and the actual authentication call
        //use it for additional checks and data insertions into template
        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATE,
            [$this, 'prepareAdapter'],
            10);

        //more listeners if you want to customize the flow...
    }

    /**
     * Pre authentication listener that happens before the default authentication
     * Can be used to prepare some data for the form and pre-validate data
     *
     * @param AuthenticationEvent $e
     */
    public function prepareAdapter(AuthenticationEvent $e)
    {
        $request = $e->getRequest();
        $errors = $e->getErrors();

        if($request->getMethod() === 'POST' && empty($errors)) {
            $identity = $e->getParam('identity', '');
            $credential = $e->getParam('password', '');
            if(empty($identity) || empty($credential)) {
                $e->addError('Credentials are required and cannot be empty');
                return;
            }

            $dbCredentials = new DbCredentials($identity, $credential);
            $e->setRequest($request->withAttribute(DbCredentials::class, $dbCredentials));
        }
    }

}