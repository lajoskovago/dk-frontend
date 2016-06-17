<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/19/2016
 * Time: 1:06 AM
 */

return [
    'dependencies' => [
        //whatever dependencies you need additionally
        'factories' => [
            \Frontend\Authentication\AuthenticationEventListener::class =>
                \Frontend\Authentication\Factory\AuthenticationEventListenerFactory::class,
        ]
    ],

    'dk_authentication' => [
        //this package specific configuration template
        'web' => [
            //change next two only if you changed the default login/logout routes
            'login_route' => 'login',
            'logout_route' => 'logout',

            //template name to use for the login form
            'login_template' => 'app::login',

            //where to redirect after login success
            'after_login_route' => ['name' => 'home', 'params' => []],
            //where to redirect after logging out
            'after_logout_route' => ['name' => 'login', 'params' => []],

            //enable the wanted url feature, to login to the previously requested uri after login
            'allow_redirect' => true,
            'redirect_query_name' => 'redirect',
        ]
    ]
];