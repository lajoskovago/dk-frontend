<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 11:10 PM
 */

namespace Frontend\Authentication;

use Frontend\User\Entity\UserEntity;
use Frontend\User\Mapper\UserDetailsMapperInterface;
use N3vrax\DkWebAuthentication\Action\LoginAction;
use N3vrax\DkWebAuthentication\Event\AuthenticationEvent;
use N3vrax\DkZendAuthentication\Adapter\CallbackCheck\DbCredentials;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class AuthenticationListener extends AbstractListenerAggregate
{
    /** @var  UserDetailsMapperInterface */
    protected $userDetailsMapper;

    public function __construct(UserDetailsMapperInterface $userDetailsMapper)
    {
        $this->userDetailsMapper = $userDetailsMapper;
    }

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

        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATE,
            [$this, 'attachUserDetails'],
            -500);

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

    /**
     * The authentication service fetches only the user entity from the user table
     * We initialize the details property too for the authenticated identity
     *
     * @param AuthenticationEvent $e
     */
    public function attachUserDetails(AuthenticationEvent $e)
    {
        if($e->getRequest()->getMethod() === 'POST') {
            $authResult = $e->getAuthenticationResult();

            if ($authResult && $authResult->isValid() &&
                empty($e->getErrors())
            ) {
                /** @var UserEntity $identity */
                $identity = $e->getIdentity();
                if ($identity) {
                    $details = $this->userDetailsMapper->getUserDetails($identity->getId());
                    $identity->setDetails($details);
                }
            }
        }
    }

}