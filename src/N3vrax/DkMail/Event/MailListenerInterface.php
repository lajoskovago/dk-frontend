<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/2/2016
 * Time: 12:37 AM
 */

namespace N3vrax\DkMail\Event;

use Zend\EventManager\ListenerAggregateInterface;

interface MailListenerInterface extends ListenerAggregateInterface
{
    public function onPreSend(MailEvent $e);

    public function onPostSend(MailEvent $e);

    public function onSendError(MailEvent $e);
}