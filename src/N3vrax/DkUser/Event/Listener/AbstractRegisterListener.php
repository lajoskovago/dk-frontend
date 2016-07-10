<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/10/2016
 * Time: 4:29 AM
 */

namespace N3vrax\DkUser\Event\Listener;

use N3vrax\DkUser\Event\RegisterEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

abstract class AbstractRegisterListener extends AbstractListenerAggregate implements UserListenerInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(RegisterEvent::EVENT_REGISTER_PRE, [$this, 'onPre'], $priority);
        $this->listeners[] = $events->attach(RegisterEvent::EVENT_REGISTER_POST, [$this, 'onPost'], $priority);
        $this->listeners[] = $events->attach(RegisterEvent::EVENT_REGISTER_ERROR, [$this, 'onError'], $priority);
    }
}