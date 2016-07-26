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
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

class UserServiceDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        $service = $callback();

        if($service instanceof UserService) {
            $service->setUserDetailsMapper($container->get(UserDetailsMapperInterface::class));
        }

        return $service;
    }

}