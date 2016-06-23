<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/21/2016
 * Time: 10:59 PM
 */

namespace N3vrax\DkUser\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Listener\AuthenticationListener;
use N3vrax\DkUser\Middleware\Bootstrap;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class BootstrapFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $bootstrap = new Bootstrap();
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $authenticationListener = $container->get(AuthenticationListener::class);
        $bootstrap->setEventManager($eventManager);
        $bootstrap->setAuthenticationListener($authenticationListener);

        return $bootstrap;
    }
}