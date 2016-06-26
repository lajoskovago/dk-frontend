<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/26/2016
 * Time: 8:54 PM
 */

namespace N3vrax\DkUser\Form\InputFilter;

use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;

class ResetPasswordInputFilter extends InputFilter
{
    use EventManagerAwareTrait;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->add([
            'name'       => 'newPassword',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Password is required and cannot be empty'
                    ]
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'message' => 'Password must have at least 4 characters'
                    ],
                ],
            ],
        ]);

        $this->add([
            'name'       => 'newPasswordVerify',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Password confirmation is required'
                    ]
                ],
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'newPassword',
                        'message' => 'Password confirmation does not match'
                    ],
                ],
            ],
        ]);

        $this->getEventManager()->trigger('init', $this);
    }
}