<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/10/2016
 * Time: 4:22 AM
 */

namespace N3vrax\DkUser\Event\Listener;

use Zend\EventManager\Event;
use Zend\EventManager\ListenerAggregateInterface;

interface UserListenerInterface extends ListenerAggregateInterface
{
    public function onPre(Event $e);

    public function onPost(Event $e);

    public function onError(Event $e);
}