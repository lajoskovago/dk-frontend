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
        //'user_table_name' => 'user',

        //'user_entity_class' => \N3vrax\DkUser\Entity\UserEntity::class,
        //'user_entity_hydrator' => \N3vrax\DkUser\Entity\UserEntityHydrator::class,

        'zend_db_adapter' => 'database',
    ],

    'dk_authentication' => [
        //this package specific configuration template
        'web' => [
            //where to redirect after login success
            'after_login_route' => 'home',
        ]
    ],

    'templates' => [
        'paths' => [
            //if you want to override templates path for module
            //'dk-user' => __DIR__ . '/../templates/dk-user',
        ]
    ]

];