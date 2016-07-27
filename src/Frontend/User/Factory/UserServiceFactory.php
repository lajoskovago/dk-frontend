<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/27/2016
 * Time: 12:15 AM
 */

namespace Frontend\User\Factory;

use Frontend\User\Mapper\UserDetailsMapperInterface;
use Frontend\User\Service\UserService;
use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Service\PasswordInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

class UserServiceFactory extends \N3vrax\DkUser\Factory\UserServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);
        $this->options = $options;

        $isDebug = isset($container->get('config')['debug'])
            ? (bool) $container->get('config')['debug']
            : false;

        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $service = new UserService(
            $container->get(UserMapperInterface::class),
            $options,
            $container->get(PasswordInterface::class)
        );

        $service->setUserEntityPrototype($container->get($options->getUserEntity()));
        $service->setUserEntityHydrator($container->get($options->getUserEntityHydrator()));

        $service->setEventManager($eventManager);
        $service->setDebug($isDebug);
        
        $service->setUserDetailsMapper($container->get(UserDetailsMapperInterface::class));

        $this->attachUserListeners($service, $container);

        return $service;
    }

}