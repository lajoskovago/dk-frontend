<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/10/2016
 * Time: 4:32 AM
 */

namespace N3vrax\DkUser\Event\Listener;

use N3vrax\DkUser\Event\ConfirmAccountEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

abstract class AbstractConfirmAccountListener extends AbstractListenerAggregate implements UserListenerInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_PRE, [$this, 'onPre'], $priority);
        $this->listeners[] = $events->attach(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_POST, [$this, 'onPost'], $priority);
        $this->listeners[] = $events->attach(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_ERROR, [$this, 'onError'], $priority);
    }
}