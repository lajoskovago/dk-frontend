<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/10/2016
 * Time: 4:42 AM
 */

namespace N3vrax\DkUser\Event\Listener;

use Zend\EventManager\AbstractListenerAggregate;

interface UserListenerAwareInterface
{
    public function attachUserListener(AbstractListenerAggregate $listener, $priority = 1);

    public function detachUserListener(AbstractListenerAggregate $listener);

    public function clearUserListeners();
}