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

//attach this small forbidden listener to change the way login page is views if someone wants to access it even is logged
//we better redirect to home in this case, instead of displaying the 403 error page
/** @var \Zend\Expressive\Helper\UrlHelper $urlHelper */
$urlHelper = $container->get(\Zend\Expressive\Helper\UrlHelper::class);
$eventManager->getSharedManager()->attach(
    \N3vrax\DkRbacGuard\Middleware\ForbiddenHandler::class,
    \N3vrax\DkRbacGuard\Event\AuthorizationEvent::EVENT_FORBIDDEN,
    function(\N3vrax\DkRbacGuard\Event\AuthorizationEvent $e) use ($urlHelper) {
        $request = $e->getRequest();
        /** @var \Zend\Expressive\Router\RouteResult $routeResult */
        $routeResult = $request->getAttribute(\Zend\Expressive\Router\RouteResult::class, null);
        if($routeResult) {
            if($routeResult->getMatchedRouteName() === 'login') {
                return new \Zend\Diactoros\Response\RedirectResponse($urlHelper->generate('home'));
            }
        }
        return true;
    }, 50
);