<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:59 PM
 */

namespace N3vrax\DkUser\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Form\RegisterForm;
use N3vrax\DkUser\Form\ResetPasswordForm;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Service\PasswordInterface;
use N3vrax\DkUser\Service\UserService;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class UserServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();


        $service = new UserService(
            $container->get(UserMapperInterface::class),
            $options,
            $container->get(RegisterForm::class),
            $container->get(ResetPasswordForm::class),
            $container->get($options->getUserEntityClass()),
            $container->get(PasswordInterface::class)
        );

        $service->setEventManager($eventManager);
        return $service;
    }
}