<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/10/2016
 * Time: 4:42 AM
 */

namespace N3vrax\DkUser\Event\Listener;

interface UserListenerAwareInterface
{
    public function attachUserListener(UserListenerInterface $listener, $priority = 1);

    public function detachUserListener(UserListenerInterface $listener);

    public function clearUserListeners();
}