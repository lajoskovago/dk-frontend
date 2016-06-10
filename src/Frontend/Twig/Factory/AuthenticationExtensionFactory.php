<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/9/2016
 * Time: 10:59 PM
 */

namespace Frontend\Twig\Factory;

use Frontend\Twig\AuthenticationExtension;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthentication\AuthenticationInterface;

class AuthenticationExtensionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AuthenticationExtension($container->get(AuthenticationInterface::class));
    }
}