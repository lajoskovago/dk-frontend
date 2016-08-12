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
            'main_menu' => \N3vrax\DkNavigation\Provider\ArrayProvider::class,
            'account_menu' => \N3vrax\DkNavigation\Provider\ArrayProvider::class,
            'account_side_menu' => \N3vrax\DkNavigation\Provider\ArrayProvider::class,
        ],

        //map a provider name to its config
        //this is for bootstrap navbar - even if we support multi-level menus, bootstrap is limited to one level
        'containers' => [
            'account_side_menu' => [
                [
                    'options' => [
                        'label' => 'Personal Information',
                        'route' => 'user',
                        'params' => ['action' => 'account'],
                    ],
                ],
                [
                    'options' => [
                        'label' => 'Change password',
                        'route' => 'user',
                        'params' => ['action' => 'change-password'],
                    ],
                ],
                [
                    'options' => [
                        'label' => 'Change email',
                        'route' => 'user',
                        'params' => ['action' => 'change-email'],
                    ],
                ],
                [
                    'options' => [
                        'label' => 'Delete account',
                        'route' => 'user',
                        'params' => ['action' => 'delete-account'],
                    ],
                    'attributes' => [
                        'style' => 'color: red'
                    ]
                ],
            ],

            'main_menu' => [
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
                                'icon' => 'fa fa-home'
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'separator',
                                'type' => 'separator',
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'About Us',
                                'route' => 'pages',
                                'params' => ['action' => 'about-us'],
                                'icon' => 'fa fa-info-circle'
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Who we are',
                                'route' => 'pages',
                                'params' => ['action' => 'who-we-are'],
                                'icon' => 'fa fa-copyright'
                            ]
                        ],
                    ]
                ],
                [
                    'options' => [
                        'label' => 'Contact',
                        'uri' => '#',
                        'icon' => 'fa fa-envelope',
                    ]
                ],
            ],

            'account_menu' => [
                [
                    'options' => [
                        'label' => 'Welcome, ',
                        'id' => 'account',
                        'uri' => '#',
                        'icon' => 'fa fa-user',
                        'permission' => 'authenticated'
                    ],
                    'attributes' => [
                        'class' => 'navbar-colored-item',
                    ],
                    'pages' => [
                        [
                            'options' => [
                                'label' => 'Settings',
                                'route' => 'user',
                                'params' => ['action' => 'account'],
                                'icon' => 'fa fa-wrench',
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Sign Out',
                                'route' => 'logout',
                                'icon' => 'fa fa-sign-out'
                            ]
                        ]
                    ]
                ],
                [
                    'options' => [
                        'label' => 'Already registered?',
                        'type' => 'text',
                        'permission' => 'unauthenticated',
                    ],
                ],
                [
                    'options' => [
                        'label' => 'Login',
                        'route' => 'login',
                        'icon' => 'fa fa-sign-in',
                        'permission' => 'unauthenticated'
                    ],
                    'attributes' => [
                        'class' => 'navbar-colored-item',
                    ]
                ],
            ]
        ],

        //register custom providers here
        'provider_manager' => [

        ]
    ],
];