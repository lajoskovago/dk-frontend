<?php

return [
    'dependencies' => [
        'factories' => [
            'Zend\Expressive\FinalHandler' =>
                Zend\Expressive\Container\TemplatedErrorHandlerFactory::class,

            Zend\Expressive\Template\TemplateRendererInterface::class =>
                Zend\Expressive\Twig\TwigRendererFactory::class,
        ],
    ],

    'templates' => [
        'extension' => 'html.twig',
        'paths'     => [
            'app'       => ['templates/app'],
            'partial'   => ['templates/partial'],
            'layout'    => ['templates/layout'],
            'error'     => ['templates/error'],
        ],
    ],

    'twig' => [
        'cache_dir'      => 'data/cache/twig',
        'assets_url'     => '/',
        'assets_version' => null,
        'extensions'     => [
            // extension service names or instances
        ],
        'globals' => [
            //global variables passed to twig templates
        ],
    ],
];
