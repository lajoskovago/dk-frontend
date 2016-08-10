<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/5/2016
 * Time: 11:53 PM
 */

namespace Frontend\User\Event;

use N3vrax\DkUser\Event\AbstractUserEvent;

class UserUpdateEvent extends AbstractUserEvent
{
    const EVENT_UPDATE_PRE = 'event.user.update.pre';
    const EVENT_UPDATE_POST = 'event.user.update.post';
    const EVENT_UPDATE_ERROR = 'event.user.update.error';
}