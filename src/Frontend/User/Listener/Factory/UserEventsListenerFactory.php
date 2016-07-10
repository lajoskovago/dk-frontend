<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/10/2016
 * Time: 5:37 PM
 */

namespace Frontend\User\Listener\Factory;

use Frontend\User\Listener\UserEventsListener;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

class UserEventsListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new UserEventsListener(
            $container->get('dkmail.mailservice.default'),
            $container->get(ServerUrlHelper::class),
            $container->get(UrlHelper::class)
        );
    }
}