<?php

return [

    'dependencies' => [
        //whatever dependencies you need additionally
        'factories' => [
            //event listeners for authentication and user events
            \Frontend\Authentication\AuthenticationListener::class =>
                \Frontend\Authentication\Factory\AuthenticationListenerFactory::class,

            \Frontend\User\Listener\UserEventsListener::class =>
                \Frontend\User\Listener\Factory\UserEventsListenerFactory::class,

            //****************************
            //we overwrite the default user entity with this ones, to include details field
            \Frontend\User\Entity\UserEntity::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,

            \Frontend\User\Entity\UserEntityHydrator::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,

            //********************************
            //extended user mapper and user details mapper
            \Frontend\User\Mapper\UserDetailsMapperInterface::class =>
                \Frontend\User\Factory\UserDetailsDbMapperFactory::class,

            \N3vrax\DkUser\Mapper\UserMapperInterface::class =>
                \Frontend\User\Factory\UserDbMapperFactory::class,

            \N3vrax\DkUser\Service\UserServiceInterface::class =>
                \Frontend\User\Factory\UserServiceFactory::class,
        ],

        'shared' => [
            \Frontend\User\Entity\UserEntity::class => false,
        ],
    ],

    'dk_user' => [
        //listeners for various user related events
        'user_listeners' => [
            \Frontend\User\Listener\UserEventsListener::class,
        ],

        //user entity and its hydrator to use for user transactions
        'user_entity' => \Frontend\User\Entity\UserEntity::class,
        'user_entity_hydrator' => \Frontend\User\Entity\UserEntityHydrator::class,

        //bcrypt cost, default to 11
        //'password_cost' => 11,

        //'enable_user_status' => true,

        //enable user form labes display
        'show_form_input_labels' => true,

        /**
         * Db options in case we use PDO/Mysql
         */
        'db_options' => [
            'db_adapter' => 'database',

            //'user_table' => 'user',
            //'user_reset_token_table' => 'user_reset_token',
            //'user_confirm_token_table' => 'user_confirm_token',
        ],

        /**
         * These are options related to user registrations
         */
        'register_options' => [
            'enable_registration' => true,

            //'enable_username' => true,

            //'user_form_timeout' => 1800,

            //'use_registration_form_captcha' => true,

            /*'form_captcha_options' => [
                'class'   => 'Figlet',
                'options' => [
                    'wordLen'    => 5,
                    'expiration' => 300,
                    'timeout'    => 300,
                ],
            ],*/

            'login_after_registration' => false,

            'default_user_status' => 'pending',

            'messages' => [
                \N3vrax\DkUser\Options\RegisterOptions::MESSAGE_REGISTER_SUCCESS =>
                    'Account created. Check your email for confirmation'
            ]
        ],

        'login_options' => [
            //'login_form_timeout' => 1800,

            //'enable_remember_me' => true,

            'auth_identity_fields' => ['username', 'email'],

            'allowed_login_statuses' => ['active'],
        ],

        'password_recovery_options' => [
            'enable_password_recovery' => true,

            //'reset_password_token_timeout' => 3600,
        ],

        'confirm_account_options' => [
            'enable_account_confirmation' => true,

            'active_user_status' => 'active'
        ],

        'form_manager' => [
            'factories' => [
                \Frontend\User\Form\UserForm::class => \Frontend\User\Factory\Form\UserFormFactory::class,
            ]
        ],
    ],

    'dk_authentication' => [
        //this package specific configuration template
        'web' => [
            //template name to use for the login form
            'login_template' => 'dk-user::login',

            //where to redirect after login success
            'after_login_route' => 'home',
            //where to redirect after logging out
            'after_logout_route' => 'login',
        ]
    ],

];