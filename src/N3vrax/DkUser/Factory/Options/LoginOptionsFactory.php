<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/23/2016
 * Time: 3:46 PM
 */

namespace N3vrax\DkUser\Factory\Options;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Options\LoginOptions;

class LoginOptionsFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new LoginOptions($container->get('config')['dk_user']);
    }
}