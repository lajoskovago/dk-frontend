<?php

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
        ],
        'factories' => [
            Frontend\Action\HomePageAction::class => Frontend\Action\HomePageFactory::class,
        ],
    ],

    'routes' => [
        [
            'name' => 'home',
            'path' => '/',
            'middleware' => Frontend\Action\HomePageAction::class,
            'allowed_methods' => ['GET'],
        ],
    ],
];
