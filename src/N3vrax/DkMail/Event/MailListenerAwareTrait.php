<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/2/2016
 * Time: 12:12 AM
 */

namespace N3vrax\DkMail\Event;

use Zend\EventManager\EventManagerAwareTrait;

trait MailListenerAwareTrait
{
    use EventManagerAwareTrait;

    /** @var MailListenerInterface[] */
    protected $listeners = [];

    public function attachMailListener(MailListenerInterface $listener, $priority = 1)
    {

        $listener->attach($this->getEventManager(), $priority);
        $this->listeners[] = $listener;
        return $this;
    }

    public function detachMailListener(MailListenerInterface $listener)
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

    public function clearMailListeners()
    {
        foreach ($this->listeners as $listener) {
            $listener->detach($this->getEventManager());
        }

        $this->listeners = [];
        return $this;
    }
}