<?php

return [
    'dependencies' => [
        'factories' => [
            \Frontend\Form\LoginForm::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Frontend\Form\InputFilter\LoginInputFilter::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ]
    ]
];