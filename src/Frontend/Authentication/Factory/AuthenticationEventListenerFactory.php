<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 11:23 PM
 */

namespace Frontend\Authentication\Factory;

use Frontend\Authentication\AuthenticationEventListener;
use Frontend\Form\LoginForm;
use Interop\Container\ContainerInterface;

class AuthenticationEventListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AuthenticationEventListener($container->get(LoginForm::class));
    }
}