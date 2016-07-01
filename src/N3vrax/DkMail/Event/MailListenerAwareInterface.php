<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/2/2016
 * Time: 12:31 AM
 */

namespace N3vrax\DkMail\Event;

interface MailListenerAwareInterface
{
    public function attachMailListener(MailListenerInterface $listener);

    public function detachMailListener(MailListenerInterface $listener);

    public function clearMailListeners();
}