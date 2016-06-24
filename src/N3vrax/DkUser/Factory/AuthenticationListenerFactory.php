<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/21/2016
 * Time: 10:50 PM
 */

namespace N3vrax\DkUser\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkBase\Session\FlashMessenger;
use N3vrax\DkUser\Listener\AuthenticationListener;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\LoginOptions;
use N3vrax\DkUser\Options\ModuleOptions;

class AuthenticationListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var ModuleOptions $options */
        $options = $container->get(ModuleOptions::class);
        return new AuthenticationListener(
            $container->get($options->getLoginForm()),
            $container->get(FlashMessenger::class),
            $container->get(UserMapperInterface::class),
            $options,
            $container->get(LoginOptions::class)
        );
    }
}