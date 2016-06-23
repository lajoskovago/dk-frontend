<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 8:31 PM
 */

namespace N3vrax\DkUser\Factory\Options;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Options\RegisterOptions;

class RegisterOptionsFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new RegisterOptions($container->get('config')['dk_user']);
    }
}