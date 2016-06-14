<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/12/2016
 * Time: 1:46 AM
 */

namespace Frontend\Form\InputFilter;

use Zend\InputFilter\InputFilter;

class LoginInputFilter extends InputFilter
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->add([
            'name' => 'identity',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'HtmlEntities'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 3,
                        'max' => 255,
                    ]
                ]
            ],
        ]);

        $this->add([
            'name' => 'credential',
            'required' => true,
            'filters' => [],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'max' => 255,
                    ]
                ]
            ],
        ]);
    }
}