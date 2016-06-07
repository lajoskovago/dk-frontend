<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 5/18/2016
 * Time: 2:14 PM
 */

return [
    //this file contains some authorization example for default services
    //some example values are just virtual, and is not intended to work as-is
    //modify this file with actual values, according to your needs
    'dependencies' => [
        //maybe you want to change the place identity is retrieved
        //change this line and add you own IdentityProvider
        //default is AuthenticationIdentityProvider which gets the identity from th dk-authentication service.
        //IdentityProviderInterface::class => \Your\Identity\Provider,
    ],

    'dk_authorization' => [
        'assertion_map' => [
            //map permissions to assertions
            //'edit' => EditAssertion::class,
        ],

        'assertion_manager' => [],

        //name of the guest role to use if no identity is provided
        'guest_role' => 'guest',

        'role_provider_manager' => [],

        //example for a flat RBAC model using the InMemoryRoleProvider
        'role_provider' => [
            \N3vrax\DkRbac\Role\InMemoryRoleProvider::class => [
                'admin' => [
                    'permissions' => [
                        'admin',
                        'user',
                    ]
                ],
                'user' => [
                    'permissions' => [
                        'user',
                    ]
                ],
                'guest' => []
            ]
        ],
    ]
];