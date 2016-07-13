<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/14/2016
 * Time: 12:03 AM
 */

namespace N3vrax\DkUser\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkAuthentication\AuthenticationInterface;
use N3vrax\DkBase\Session\FlashMessenger;
use N3vrax\DkUser\Middleware\AutoLogin;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Service\UserServiceInterface;
use Zend\Expressive\Helper\UrlHelper;

class AutoLoginFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AutoLogin(
            $container->get(AuthenticationInterface::class),
            $container->get(UserServiceInterface::class),
            $container->get(UrlHelper::class),
            $container->get(FlashMessenger::class),
            $container->get(UserOptions::class)
        );
    }
}