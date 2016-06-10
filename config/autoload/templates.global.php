<?php

return [
    'dependencies' => [
        'factories' => [
            'Zend\Expressive\FinalHandler' =>
                Zend\Expressive\Container\TemplatedErrorHandlerFactory::class,

            Zend\Expressive\Template\TemplateRendererInterface::class =>
                Zend\Expressive\Twig\TwigRendererFactory::class,

            \Frontend\Twig\AuthenticationExtension::class =>
                \Frontend\Twig\Factory\AuthenticationExtensionFactory::class,

            \Frontend\Twig\AuthorizationExtension::class =>
                \Frontend\Twig\Factory\AuthorizationExtensionFactory::class,
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
            \Frontend\Twig\AuthenticationExtension::class,
            \Frontend\Twig\AuthorizationExtension::class,
        ],
        'globals' => [
            //global variables passed to twig templates
        ],
    ],
];
