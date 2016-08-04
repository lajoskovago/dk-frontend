<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/4/2016
 * Time: 12:03 AM
 */

namespace Frontend\User\Form\InputFilter;

use Zend\InputFilter\InputFilter;

class UserDetailsInputFilter extends InputFilter
{
    public function __construct()
    {

    }

    public function init()
    {
        $this->add([
            'name' => 'firstName',
            'filters' => [
                ['name' => 'StringTrim']
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => 'First name is required and cannot be empty'
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'max' => 255,
                        'message' => 'First name character limit exceeded'
                    ]
                ]
            ],
        ]);

        $this->add([
            'name' => 'lastName',
            'filters' => [
                ['name' => 'StringTrim']
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => 'Last name is required and cannot be empty'
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 3,
                        'max' => 255,
                        'message' => 'Last name character limit exceeded'
                    ]
                ]
            ],
        ]);
    }
}