<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/21/2016
 * Time: 9:33 PM
 */

namespace N3vrax\DkUser\Form\InputFilter;

use N3vrax\DkUser\Options\LoginOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;

class LoginInputFilter extends InputFilter
{
    use EventManagerAwareTrait;

    /** @var  LoginOptions */
    protected $loginOptions;

    public function __construct(LoginOptions $options)
    {
        $this->loginOptions = $options;
        $this->init();
    }

    public function init()
    {
        $this->add([
            'name' => 'identity',
            'required' => true,
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
                    ]
                ]
            ],
        ]);

        $this->getEventManager()->trigger('init', $this);
    }
}