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
                'pages' => ['premium-content'],
            ]
        ],

        //define custom guards here
        'guard_manager' => [],

        //if enabled, use a default listener that will redirect on forbidden exception to a predefined route
        //'enable_redirect_forbidden_listener' => false,

        //where to redirect if forbidden redirect listener is used
        'redirect_route' => ['name' => 'login', 'params' => []],

        //if redirect enabled, this will append the wanted url to the link
        //'allow_redirect' => true,

        //query param name for the above wanted url, if enabled
        //'redirect_query_name' => 'redirect',
    ]
];