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

/** @var \Zend\Expressive\Helper\UrlHelper $urlHelper */
$urlHelper = $container->get(\Zend\Expressive\Helper\UrlHelper::class);

//if forbidden is triggered on login or logout routes, straightly redirect to predefined routes
//we don't let the default forbidden listener, because is not appropriate for these routes
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
            if($routeResult->getMatchedRouteName() === 'logout') {
                return new \Zend\Diactoros\Response\RedirectResponse($urlHelper->generate('login'));
            }
        }
        return true;
    }, 50
);

//if unauthorized is triggered on logout route, just redirect to login, the default listener is not appropriate for this route
$eventManager->getSharedManager()->attach(
    \N3vrax\DkWebAuthentication\UnauthorizedHandler::class,
    \N3vrax\DkWebAuthentication\Event\AuthenticationEvent::EVENT_UNAUTHORIZED,
    function(\N3vrax\DkWebAuthentication\Event\AuthenticationEvent $e) use ($urlHelper) {
        $request = $e->getRequest();
        /** @var \Zend\Expressive\Router\RouteResult $routeResult */
        $routeResult = $request->getAttribute(\Zend\Expressive\Router\RouteResult::class, null);
        if($routeResult) {
            if($routeResult->getMatchedRouteName() === 'logout') {
                return new \Zend\Diactoros\Response\RedirectResponse($urlHelper->generate('login'));
            }
        }
        return true;
    }, 50
);