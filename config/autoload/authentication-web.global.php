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
            \Frontend\Authentication\PreAuthCallback::class => \Frontend\Factory\PreAuthCallbackFactory::class,
        ]
    ],

    'dk_authentication' => [
        //this package specific configuration template
        'web' => [
            //change next two only if you changed the default login/logout routes
            'login_route' => 'login',
            'logout_route' => 'logout',

            //template name to use for the login form
            'login_template_name' => 'app::login',

            //where to redirect after login success
            'after_login_route' => ['name' => 'home', 'params' => []],
            //where to redirect after logging out
            'after_logout_route' => ['name' => 'login', 'params' => []],

            //callback to call before authentication happens(closure or service name of a callable etc.)
            //this is useful to extract credentials from the request, possibly validating them
            //then prepare them for the authentication adapter
            'pre_auth_callback' => \Frontend\Authentication\PreAuthCallback::class,

            //enable the wanted url feature, to login to the previously requested uri after login
            'allow_redirect' => true,

            'redirect_query_name' => 'redirect',

            //enable the default unauthorized(401) error handler, to make the redirects
            'enable_unauthorized_handler' => true,
        ]
    ]
];