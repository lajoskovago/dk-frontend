<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/19/2016
 * Time: 12:42 AM
 */

return [
    'dk_authentication' => [
        //required by the auth adapters, it may be optional for your custom adapters
        //specify the identity entity to use and its hydrator
        'identity_prototype' => \Frontend\User\Entity\UserEntity::class,
        'identity_hydrator' => \Frontend\User\Entity\UserEntityHydrator::class,

        //this is adapter specific
        //currently we support HTTP basic and digest
        //below is config template for callbackcheck adapter
        'adapter' => [
            \N3vrax\DkZendAuthentication\Adapter\CallbackCheckAdapter::class => [
                //zend db adapter service name
                'db_adapter' => 'database',

                //your user table name
                'table_name' => 'user',

                //what user fields should use for authentication(db fields)
                'identity_columns' => ['username', 'email'],

                //name of the password db field
                'credential_column' => 'password',

                'callback_check' => \N3vrax\DkUser\Service\PasswordInterface::class,

                //your password checking callback, use a closure, a service name of a callable or a callable class name
                //we recommend using a service name or class name instead of closures, to be able to cache the config
                //the below closure is just an example, to show you the callable signature
                //'callback_check' => function($hash_passwd, $password) {
                //    return $hash_passwd === md5($password);
                //}
            ],
        ],

        //storage specific options, example below, for session storage
        'storage' => [
            \N3vrax\DkZendAuthentication\Storage\Session::class => [
                //session namespace
                'namespace' => 'dk_auth',

                //what session member to use
                'member' => 'user'
            ],
        ],

        'adapter_manager' => [
            //register custom adapters here, like you would do in a normal container
        ],

        'storage_manager' => [
            //register custom storage adapters
        ],

        'resolver_manager' => [
            //define custom http authentication resolvers here
        ]
    ]
];