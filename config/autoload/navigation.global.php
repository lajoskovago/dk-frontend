<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/7/2016
 * Time: 9:21 PM
 */

return [
    'dk_navigation' => [

        //enable menu item active if any child is active
        'active_recursion' => true,

        //depths between the menu hierarchy is iterated
        'min_depth' => -1,
        'max_depth' => -1,

        //css classes to use for menu rendering
        'active_class' => 'active',
        'ul_class' => 'nav navbar-nav',

        //map a provider name to a provider type
        'providers_map' => [
            'default' => \N3vrax\DkNavigation\Provider\ArrayProvider::class,
        ],

        //map a provider name to its config
        'containers' => [
            'default' => [
                [
                    'options' => [
                        'label' => 'Home',
                        'route' => 'home',
                    ],
                    'pages' => [
                        [
                            'options' => [
                                'label' => 'Logout',
                                'route' => 'logout'
                            ]
                        ]
                    ]
                ],
                [
                    'options' => [
                        'label' => 'Login',
                        'route' => 'login',
                    ],
                ]
            ]
        ],

        //register custom providers here
        'provider_manager' => [

        ]
    ],
];