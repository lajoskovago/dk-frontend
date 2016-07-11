<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/26/2016
 * Time: 8:48 PM
 */

namespace N3vrax\DkUser\Form;

use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Form;

class ResetPasswordForm extends Form
{
    use EventManagerAwareTrait;

    public function __construct($name = 'reset-password', array $options = [])
    {
        parent::__construct($name, $options);
        $this->init();
    }

    public function init()
    {
        $this->add(array(
            'type' => 'password',
            'name' => 'newPassword',
            'attributes' => array(
                'placeholder' => 'New Password',
                //'required' => true,
            ),
        ));

        $this->add(array(
            'type' => 'password',
            'name' => 'newPasswordVerify',
            'attributes' => array(
                'placeholder' => 'Confirm Password',
                //'required' => true,
            ),
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Reset password',
            ),
        ), ['priority' => -100]);

        $this->getEventManager()->trigger('init', $this);
    }
}