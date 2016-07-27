<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 11:23 PM
 */

namespace Frontend\Authentication\Factory;

use Frontend\Authentication\AuthenticationListener;
use Frontend\User\Mapper\UserDetailsMapperInterface;
use Interop\Container\ContainerInterface;

class AuthenticationListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AuthenticationListener($container->get(UserDetailsMapperInterface::class));
    }
}