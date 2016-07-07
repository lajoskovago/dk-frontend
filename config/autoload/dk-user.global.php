<?php

return [

    'dependencies' => [
        //whatever dependencies you need additionally
        'factories' => [
            \Frontend\Authentication\AuthenticationEventListener::class =>
                \Frontend\Authentication\Factory\AuthenticationEventListenerFactory::class,
        ]
    ],

    'dk_user' => [

        //'user_entity_class' => \N3vrax\DkUser\Entity\UserEntity::class,
        //'user_entity_hydrator' => \N3vrax\DkUser\Entity\UserEntityHydrator::class,

        //'password_cost' => 11,

        //'enable_user_status' => true,

        'db_options' => [
            'db_adapter' => 'database',

            //'user_table' => 'user',
            //'user_reset_token_table' => 'user_reset_token',
            //'user_confirm_token_table' => 'user_confirm_token',
        ],

        'register_options' => [
            'enable_registration' => true,

            'enable_username' => true,

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
        ],

        'login_options' => [
            //'login_form_timeout' => 1800,

            'enable_remember_me' => true,

            'auth_identity_fields' => ['username', 'email'],

            'allowed_login_statuses' => ['active'],
        ],

        'password_recovery_options' => [
            'enable_password_recovery' => true,

            'reset_password_token_timeout' => 3600,
        ],

        'confirm_account_options' => [
            'enable_account_confirmation' => true,
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

    'templates' => [
        'paths' => [
            //if you want to override templates path for module
            //'dk-user' => __DIR__ . '/../templates/dk-user',
        ]
    ]

];