<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:59 PM
 */

namespace N3vrax\DkUser\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use N3vrax\DkUser\Service\PasswordHashingInterface;
use N3vrax\DkUser\Service\UserService;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class UserServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var ModuleOptions $options */
        $options = $container->get(ModuleOptions::class);
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();


        $service = new UserService(
            $container->get(UserMapperInterface::class),
            $options,
            $container->get(RegisterOptions::class),
            $container->get($options->getRegisterForm()),
            $container->get($options->getUserEntityClass()),
            $container->get(PasswordHashingInterface::class)
        );

        $service->setEventManager($eventManager);
        return $service;
    }
}