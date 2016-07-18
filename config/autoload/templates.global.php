<?php

return [
    'dependencies' => [
        'factories' => [
            'Zend\Expressive\FinalHandler' =>
                Zend\Expressive\Container\TemplatedErrorHandlerFactory::class,

            //we replaced the default renderer factory with ours to inject the zend view helpers
            Zend\Expressive\Template\TemplateRendererInterface::class =>
                \N3vrax\DkBase\Twig\Renderer\TwigRendererFactory::class,
        ],
    ],

    'templates' => [
        'extension' => 'html.twig',
        'paths'     => [
            'app'       => [__DIR__ . '/../../templates/app'],
            'page'      => [__DIR__ . '/../../templates/app/page'],
            'partial'   => [__DIR__ . '/../../templates/partial'],
            'layout'    => [__DIR__ . '/../../templates/layout'],
            'error'     => [__DIR__ . '/../../templates/error'],
        ],
    ],

    'twig' => [
        'cache_dir'      => __DIR__ . '/../../data/cache/twig',
        'assets_url'     => '/',
        'assets_version' => null,
        'extensions'     => [
            // extension service names or instances
        ],
        'globals' => [
            //global variables passed to twig templates
        ],
    ],

    //these are zend view helpers registered under twig
    //using the twig fallback function to request unknown twig extensions from the view helper plugin manager
    'view_helpers' => [

    ]
];
