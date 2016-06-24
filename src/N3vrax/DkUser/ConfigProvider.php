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
use N3vrax\DkUser\Factory\Options\LoginOptionsFactory;
use N3vrax\DkUser\Factory\Options\ModuleOptionsFactory;
use N3vrax\DkUser\Factory\Options\RegisterOptionsFactory;
use N3vrax\DkUser\Factory\UserControllerFactory;
use N3vrax\DkUser\Factory\UserDbMapperFactory;
use N3vrax\DkUser\Factory\UserServiceFactory;
use N3vrax\DkUser\Form\LoginForm;
use N3vrax\DkUser\Form\RegisterForm;
use N3vrax\DkUser\Listener\AuthenticationListener;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Middleware\Bootstrap;
use N3vrax\DkUser\Options\LoginOptions;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use N3vrax\DkUser\Service\UserService;
use N3vrax\DkUser\Twig\FormElementExtension;
use Zend\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    public function __invoke()
    {
        return [

            'dependencies' => [
                'factories' => [
                    ModuleOptions::class => ModuleOptionsFactory::class,
                    LoginOptions::class => LoginOptionsFactory::class,
                    RegisterOptions::class => RegisterOptionsFactory::class,

                    UserMapperInterface::class => UserDbMapperFactory::class,
                    UserService::class => UserServiceFactory::class,

                    UserController::class => UserControllerFactory::class,

                    LoginForm::class => LoginFormFactory::class,
                    RegisterForm::class => RegisterFormFactory::class,

                    UserEntity::class => InvokableFactory::class,
                    UserEntityHydrator::class => InvokableFactory::class,

                    AuthenticationListener::class => AuthenticationListenerFactory::class,

                    Bootstrap::class => BootstrapFactory::class,
                    FormElementExtension::class => InvokableFactory::class,
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

                'user_table_name' => 'user',

                'user_entity_class' => UserEntity::class,
                'user_entity_hydrator' => UserEntityHydrator::class,

                'zend_db_adapter' => 'database',

                'login_form' => LoginForm::class,
                'register_form' => RegisterForm::class,

                'enable_remember_me' => true,

                'enable_registration' => true,
                'login_after_registration' => false,
                'enable_username' => true,

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