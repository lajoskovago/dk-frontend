<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/2/2016
 * Time: 9:07 PM
 */

namespace Frontend\User\Listener;

use Frontend\User\Form\UserDetailsFieldset;
use Zend\EventManager\Event;
use Zend\Form\Form;

class RegisterFormListener
{
    public function __invoke(Event $e)
    {
        var_dump('aaa');exit;
        /** @var Form $form */
        $form = $e->getTarget();

        $detailsFieldset = new UserDetailsFieldset();
        $detailsFieldset->setName('details');
        //remove some elements from the fieldset that you don't want in the register form
        //this is not the case right now, as this fieldset contains only lastName and firstName

        $form->add($detailsFieldset);
    }
}