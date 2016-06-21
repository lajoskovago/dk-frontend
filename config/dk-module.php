<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/19/2016
 * Time: 2:04 PM
 */

require __DIR__ . '/../vendor/autoload.php';

define('MODULE_NAME', 'dk-frontend');
define('DOC_ROOT', 'modules/' . MODULE_NAME . '/public');

/** @var \Interop\Container\ContainerInterface $container */
$container = require __DIR__ . '/container.php';

/** @var \Zend\Expressive\Application $app */
$app = $container->get(\Zend\Expressive\Application::class);

return [
    //default path
    'path' => '/',
    'name' => MODULE_NAME,
    'doc_root' => DOC_ROOT,
    'application' => $app,
];