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
    //modules configs...
    \N3vrax\DkBase\ModuleConfig::class,
    \N3vrax\DkZendAuthentication\ModuleConfig::class,
    \N3vrax\DkWebAuthentication\ModuleConfig::class,
    \N3vrax\DkRbac\ModuleConfig::class,
    \N3vrax\DkRbacGuard\ModuleConfig::class,
    \N3vrax\DkNavigation\ModuleConfig::class,

    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
], $cachedConfigFile);

return new ArrayObject($configManager->getMergedConfig());
