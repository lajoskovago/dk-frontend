<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/10/2016
 * Time: 4:34 AM
 */

namespace N3vrax\DkUser\Event\Listener;

use N3vrax\DkUser\Event\PasswordResetEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

abstract class AbstractPasswordResetListener extends AbstractListenerAggregate implements UserListenerInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(PasswordResetEvent::EVENT_PASSWORD_RESET_PRE, [$this, 'onPre'], $priority);
        $this->listeners[] = $events->attach(PasswordResetEvent::EVENT_PASSWORD_RESET_POST, [$this, 'onPost'], $priority);
        $this->listeners[] = $events->attach(PasswordResetEvent::EVENT_PASSWORD_RESET_ERROR, [$this, 'onError'], $priority);
    }
}