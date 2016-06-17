<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/21/2016
 * Time: 1:34 AM
 */

return [

    'dk_authorization' => [

        //define how it will treat non-matching guard rules, allow all by default
        'protection_policy' => \N3vrax\DkRbacGuard\GuardInterface::POLICY_ALLOW,

        //list of guards
        'guards' => [
            //the RouteGuard allows you to restrict access to routes based on the user's role
            //route can also contain wildcard(*)
            //to block access to a route, set the roles to an empty array
            /*\N3vrax\DkRbacGuard\Route\RouteGuard::class => [
                'premium' => ['admin'],
                'login' => ['guest'],
                'logout' => ['admin', 'user', 'viewer'],
                'account' => ['admin', 'user'],
                'home' => ['*'],
            ],*/

            //the RoutePermissionGuard allows you to restrict access to routes based on permissions
            \N3vrax\DkRbacGuard\Route\RoutePermissionGuard::class => [
                'login' => ['unauthenticated'],
                'logout' => ['authenticated'],
                //'pages' => ['premium-content'],
            ]
        ],

        //define custom guards here
        'guard_manager' => [],


        //whether to enable the default forbidden handler
        'enable_forbidden_handler' => true,

        //how to handle forbidden exceptions(currently 2 options)
        //pass-through - passes a 403 response to the next middleware
        //redirect - goes to a named route
        'forbidden_handler_strategy' => \N3vrax\DkRbacGuard\Middleware\ForbiddenHandler::PASSTHROUGH_STRATEGY,

        //redirect route in case the forbidden handler strategy is redirection
        //'redirect_route' => '['name' => 'route name', 'params' => []]',

        //if redirect enabled, this will append the wanted url to the link
        //'enable_redirect' => true,

        //query param name for the above wanted url, if enabled
        //'redirect_query_name' => 'redirect',
    ]
];