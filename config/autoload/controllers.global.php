<?php

return [

    'dependencies' => [
        'factories' => [
            \Frontend\User\Controller\UserController::class =>
                \Frontend\User\Factory\UserControllerFactory::class,

            \Frontend\Controller\PageController::class =>
                \Frontend\Controller\Factory\PageControllerFactory::class,
        ]
    ],

    'dk_controller' => [

        'plugin_manager' => []

    ],
];