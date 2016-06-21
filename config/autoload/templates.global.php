<?php

return [
    'dependencies' => [
        'factories' => [
            'Zend\Expressive\FinalHandler' =>
                Zend\Expressive\Container\TemplatedErrorHandlerFactory::class,

            //we replaced the default renderer factory with ours to inject the zend view helpers
            Zend\Expressive\Template\TemplateRendererInterface::class =>
                \Frontend\Twig\Renderer\TwigRendererFactory::class,

            \Zend\View\HelperPluginManager::class =>
                \Frontend\Zend\View\HelperPluginManagerFactory::class,

            \Frontend\Twig\AuthenticationExtension::class =>
                \Frontend\Twig\Factory\AuthenticationExtensionFactory::class,

            \Frontend\Twig\AuthorizationExtension::class =>
                \Frontend\Twig\Factory\AuthorizationExtensionFactory::class,
        ],
        'aliases' => [
            'ViewHelperManager' => \Zend\View\HelperPluginManager::class,
        ]
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
            \Frontend\Twig\AuthenticationExtension::class,
            \Frontend\Twig\AuthorizationExtension::class,
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
