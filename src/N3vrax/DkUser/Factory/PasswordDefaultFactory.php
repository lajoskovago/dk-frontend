<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/24/2016
 * Time: 7:47 PM
 */

namespace N3vrax\DkUser\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Service\PasswordDefault;

class PasswordDefaultFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(ModuleOptions::class);
        return new PasswordDefault($options);
    }
}