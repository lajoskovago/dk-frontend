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
/** @var \Frontend\Authentication\AuthenticationListener $authenticationListeners */
$authenticationListeners = $container->get(\Frontend\Authentication\AuthenticationListener::class);
$authenticationListeners->attach($eventManager);

$eventManager->getSharedManager()->attach(
    \N3vrax\DkUser\Form\RegisterForm::class,
    'init',
    function(\Zend\EventManager\Event $e) {
        var_dump('aaa');exit;
    });
