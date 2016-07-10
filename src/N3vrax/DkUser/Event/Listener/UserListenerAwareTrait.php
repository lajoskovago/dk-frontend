<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/10/2016
 * Time: 4:44 AM
 */

namespace N3vrax\DkUser\Event\Listener;

use Zend\EventManager\EventManagerAwareTrait;

trait UserListenerAwareTrait
{
    use EventManagerAwareTrait;

    /** @var UserListenerInterface[] */
    protected $listeners = [];
    
    public function attachUserListener(UserListenerInterface $listener, $priority = 1)
    {
        $listener->attach($this->getEventManager(), $priority);
        $this->listeners[] = $listener;

        return $this;
    }

    public function detachUserListener(UserListenerInterface $listener)
    {
        $listener->detach($this->getEventManager());

        $idx = 0;
        foreach ($this->listeners as $l) {
            if($l === $listener) {
                break;
            }

            $idx++;
        }

        unset($this->listeners[$idx]);
        return $this;
    }

    public function clearUserListeners()
    {
        foreach ($this->listeners as $listener) {
            $listener->detach($this->getEventManager());
        }

        $this->listeners = [];
        return $this;
    }
}