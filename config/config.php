<?php

use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Glob;

/**
 * Configuration files are loaded in a specific order. First ``global.php``, then ``*.global.php``.
 * then ``local.php`` and finally ``*.local.php``. This way local settings overwrite global settings.
 *
 * The configuration can be cached. This can be done by setting ``config_cache_enabled`` to ``true``.
 *
 * Obviously, if you use closures in your config you can't cache it.
 */

$cachedConfigFile = 'data/cache/app_config.php';

use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Expressive\ConfigManager\PhpFileProvider;

$configManager = new ConfigManager([
    //dk modules config providers, these are required
    \N3vrax\DkBase\ModuleConfig::class,
    \N3vrax\DkZendAuthentication\ModuleConfig::class,
    \N3vrax\DkWebAuthentication\ConfigProvider::class,
    \N3vrax\DkRbac\ModuleConfig::class,
    \N3vrax\DkRbacGuard\ModuleConfig::class,
    \N3vrax\DkNavigation\ModuleConfig::class,

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

    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
], $cachedConfigFile);

return new ArrayObject($configManager->getMergedConfig());
