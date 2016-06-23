<?php

use Zend\Stdlib\ArrayUtils;
use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Expressive\ConfigManager\PhpFileProvider;


$cachedConfigFile = __DIR__ . '/../data/cache/app_config.php';
if(!is_dir(__DIR__ . '/../data/cache')) {
    mkdir(__DIR__ . '/../data/cache');
    chmod(__DIR__ . '/../data/cache', 755);
}

$configManager = new ConfigManager([
    //dk modules config providers, these are required
    \N3vrax\DkBase\ConfigProvider::class,
    \N3vrax\DkZendAuthentication\ConfigProvider::class,
    \N3vrax\DkWebAuthentication\ConfigProvider::class,
    \N3vrax\DkRbac\ConfigProvider::class,
    \N3vrax\DkRbacGuard\ConfigProvider::class,
    \N3vrax\DkNavigation\ConfigProvider::class,
    \N3vrax\DkUser\ConfigProvider::class,
    

    //*************************************
    //zend framework enabled modules, might come in handy to have all these services in the DI
    //zend-db dependencies, as we use it
    \Zend\Db\ConfigProvider::class,

    //zend-filters, not used directly, but by zend forms
    \Zend\Filter\ConfigProvider::class,

    //we use hydrators, might be useful for the hydrator manager
    \Zend\Hydrator\ConfigProvider::class,

    //input filter manager and abstract factory
    \Zend\InputFilter\ConfigProvider::class,

    //if we decide to use zend session, lets have it ready
    \Zend\Session\ConfigProvider::class,

    //validators to be used with forms
    \Zend\Validator\ConfigProvider::class,

    //needed mainly for the form view helpers
    \Zend\Form\ConfigProvider::class,

    new PhpFileProvider(__DIR__ . '/autoload/{{,*.}global,{,*.}local}.php'),
], $cachedConfigFile);

return new ArrayObject($configManager->getMergedConfig());
