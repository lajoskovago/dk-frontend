<?php

use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

// Load configuration
$config = require __DIR__ . '/config.php';

// Build container
$container = new ServiceManager();
(new Config($config['dependencies']))->configureServiceManager($container);

// Inject config
$container->setService('config', $config);

$eventManager = $container->get(\Zend\EventManager\EventManagerInterface::class);
/**
 * Register event listeners
 */
/** @var \Frontend\Authentication\AuthenticationEventListener $authenticationListeners */
$authenticationListeners = $container->get(\Frontend\Authentication\AuthenticationEventListener::class);
$authenticationListeners->attach($eventManager);

/**
 * ################################
 */

return $container;
