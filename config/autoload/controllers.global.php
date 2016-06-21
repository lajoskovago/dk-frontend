<?php

return [

    'dependencies' => [
        'factories' => [
            \Frontend\Controller\PageController::class =>
                \Frontend\Controller\Factory\PageControllerFactory::class,
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