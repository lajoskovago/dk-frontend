<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:38 PM
 */

namespace N3vrax\DkUser\Factory\Options;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Options\ModuleOptions;

class ModuleOptionsFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ModuleOptions($container->get('config')['dk_user']);
    }
}