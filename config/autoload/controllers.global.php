<?php

return [

    'dependencies' => [
        'invokables' => [
            \Frontend\Controller\PageController::class => \Frontend\Controller\PageController::class,
        ],

        'factories' => [

        ]
    ],

    'dk_controller' => [

        'plugin_manager' => []

    ],

    'routes' => [
        [
            'name' => 'pages',
            'path' => '/page[/{action}]',
            'middleware' => \Frontend\Controller\PageController::class,
        ],
    ]
];