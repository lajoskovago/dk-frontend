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
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim']
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'max' => 255
                    ]
                ]
            ],
        ]);

        $this->add([
            'name' => 'firstName',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim']
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'max' => 255
                    ]
                ]
            ],
        ]);
    }
}