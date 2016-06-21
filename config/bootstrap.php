<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/19/2016
 * Time: 2:01 PM
 */

/** @var \Zend\EventManager\EventManagerInterface $eventManager */
$eventManager = $container->get(\Zend\EventManager\EventManagerInterface::class);
/**
 * Register event listeners
 */
/** @var \Frontend\Authentication\AuthenticationEventListener $authenticationListeners */
$authenticationListeners = $container->get(\Frontend\Authentication\AuthenticationEventListener::class);
$authenticationListeners->attach($eventManager);