<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 8:44 PM
 */

namespace N3vrax\DkUser\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Controller\UserController;
use N3vrax\DkUser\Form\LoginForm;
use N3vrax\DkUser\Form\RegisterForm;
use N3vrax\DkUser\Form\ResetPasswordForm;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use N3vrax\DkUser\Service\UserService;
use N3vrax\DkUser\Service\UserServiceInterface;
use N3vrax\DkWebAuthentication\Action\LoginAction;

class UserControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var ModuleOptions $options */
        $options = $container->get(ModuleOptions::class);
        $userService = $container->get(UserServiceInterface::class);

        $controller = new UserController(
            $userService,
            $container->get(LoginAction::class),
            $options,
            $container->get(RegisterOptions::class),
            $container->get(LoginForm::class),
            $container->get(RegisterForm::class),
            $container->get(ResetPasswordForm::class)
        );

        return $controller;
    }
}