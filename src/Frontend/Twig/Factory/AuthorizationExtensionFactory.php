<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/10/2016
 * Time: 1:43 AM
 */

namespace Frontend\Twig\Factory;

use Frontend\Twig\AuthorizationExtension;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthorization\AuthorizationInterface;

class AuthorizationExtensionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AuthorizationExtension($container->get(AuthorizationInterface::class));
    }
}