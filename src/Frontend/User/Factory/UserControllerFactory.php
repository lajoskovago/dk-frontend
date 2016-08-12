<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/6/2016
 * Time: 12:16 AM
 */

namespace Frontend\User\Factory;

use Frontend\User\Controller\UserController;
use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Form\FormManager;
use N3vrax\DkUser\Service\UserServiceInterface;

class UserControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        var_dump($container->get('config')['templates']);exit;
        $controller = new UserController(
            $container->get(UserServiceInterface::class),
            $container->get(FormManager::class));

        return $controller;
    }
}