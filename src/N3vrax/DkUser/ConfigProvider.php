<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 7:54 PM
 */

namespace N3vrax\DkUser;

use N3vrax\DkUser\Controller\UserController;
use N3vrax\DkUser\Entity\UserEntity;
use N3vrax\DkUser\Entity\UserEntityHydrator;
use N3vrax\DkUser\Factory\AuthenticationListenerFactory;
use N3vrax\DkUser\Factory\BootstrapFactory;
use N3vrax\DkUser\Factory\Form\LoginFormFactory;
use N3vrax\DkUser\Factory\Form\RegisterFormFactory;
use N3vrax\DkUser\Factory\Form\ResetPasswordFormFactory;
use N3vrax\DkUser\Factory\Options\LoginOptionsFactory;
use N3vrax\DkUser\Factory\Options\ModuleOptionsFactory;
use N3vrax\DkUser\Factory\Options\RegisterOptionsFactory;
use N3vrax\DkUser\Factory\Options\TableOptionsFactory;
use N3vrax\DkUser\Factory\Options\UserOptionsFactory;
use N3vrax\DkUser\Factory\PasswordDefaultFactory;
use N3vrax\DkUser\Factory\UserControllerFactory;
use N3vrax\DkUser\Factory\UserDbMapperFactory;
use N3vrax\DkUser\Factory\UserServiceFactory;
use N3vrax\DkUser\Form\LoginForm;
use N3vrax\DkUser\Form\RegisterForm;
use N3vrax\DkUser\Form\ResetPasswordForm;
use N3vrax\DkUser\Listener\AuthenticationListener;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Middleware\Bootstrap;
use N3vrax\DkUser\Options\LoginOptions;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use N3vrax\DkUser\Options\TableOptions;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Service\PasswordInterface;
use N3vrax\DkUser\Service\UserService;
use N3vrax\DkUser\Service\UserServiceInterface;
use N3vrax\DkUser\Twig\FormElementExtension;
use Zend\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    public function __invoke()
    {
        return [

            'dependencies' => [
                'factories' => [
                    UserOptions::class => UserOptionsFactory::class,

                    UserMapperInterface::class => UserDbMapperFactory::class,
                    UserServiceInterface::class => UserServiceFactory::class,

                    UserController::class => UserControllerFactory::class,

                    LoginForm::class => LoginFormFactory::class,
                    RegisterForm::class => RegisterFormFactory::class,
                    ResetPasswordForm::class => ResetPasswordFormFactory::class,

                    UserEntity::class => InvokableFactory::class,
                    UserEntityHydrator::class => InvokableFactory::class,

                    AuthenticationListener::class => AuthenticationListenerFactory::class,

                    Bootstrap::class => BootstrapFactory::class,
                    FormElementExtension::class => InvokableFactory::class,

                    PasswordInterface::class => PasswordDefaultFactory::class,
                ],

                'shared' => [
                    UserEntity::class => false,
                ],
            ],

            'middleware_pipeline' => [
                [
                    'middleware' => Bootstrap::class,
                    'priority' => PHP_INT_MAX,
                ]
            ],

            'dk_user' => [


                'db_options' => [

                ],

                'register_options' => [

                ],

                'login_options' => [

                ],

                'password_recovery_options' => [

                ],

                'confirm_account_options' => [

                ],
            ],

            'dk_authentication' => [

                'web' => [
                    'login_route' => 'login',
                    'logout_route' => 'logout',

                    'login_template' => 'dk-user::login',

                    'after_logout_route' => 'login',
                    'after_login_route' => 'home',
                    
                    'allow_redirect' => true,
                    'redirect_query_name' => 'redirect',
                ]

            ],

            'routes' => [
                'login_route' => [
                    'name' => 'login',
                    'path' => '/user/login',
                ],
                'logout_route' => [
                    'name' => 'logout',
                    'path' => '/user/logout',
                ],
                'user_route' => [
                    'name' => 'user',
                    'path' => '/user[/{action}]',
                    'middleware' => [
                        //we keep this as array so that other controllers can be inserted to the same path
                        UserController::class
                    ],
                ],
            ],

            'templates' => [
                'paths' => [
                    'dk-user' => __DIR__ . '/../../../templates/dk-user',
                ]
            ],

            'twig' => [
                'extensions' => [
                    FormElementExtension::class,
                ]
            ]

        ];
    }
}