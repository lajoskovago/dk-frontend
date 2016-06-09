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

        //map a provider name to a provider type
        'providers_map' => [
            'default' => \N3vrax\DkNavigation\Provider\ArrayProvider::class,
        ],

        //map a provider name to its config
        'containers' => [
            'default' => [
                [
                    'options' => [
                        'label' => 'Contribute',
                        'uri' => 'https://github.com/n3vrax/dk-frontend',
                        'icon' => 'fa fa-users',
                    ],
                    'attributes' => [
                        'target' => '_blank'
                    ],
                ],
                [
                    'options' => [
                        'label' => 'Pages',
                        'uri' => '#',
                        'icon' => 'fa fa-book',
                    ],
                    'pages' => [
                        [
                            'options' => [
                                'label' => 'Home',
                                'route' => 'home',
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'About Us',
                                'uri' => '#',
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Contact',
                                'uri' => '#',
                            ]
                        ]
                    ]
                ],
                [
                    'options' => [
                        'label' => 'Login',
                        'route' => 'login',
                        'icon' => 'fa fa-user',
                        'permission' => 'unauthenticated'
                    ],
                ],
                [
                    'options' => [
                        'label' => 'Logout',
                        'route' => 'logout',
                        'icon' => 'fa fa-user',
                        'permission' => 'authenticated'
                    ],
                ]
            ]
        ],

        //register custom providers here
        'provider_manager' => [

        ]
    ],
];