<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:59 PM
 */

namespace N3vrax\DkUser\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Service\UserService;

class UserServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var ModuleOptions $options */
        $options = $container->get(ModuleOptions::class);

        return new UserService(
            $container->get(UserMapperInterface::class),
            $options,
            $container->get($options->getRegisterForm()),
            $container->get($options->getUserEntityClass())
        );
    }
}