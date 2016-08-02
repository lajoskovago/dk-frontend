<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/2/2016
 * Time: 9:18 PM
 */

namespace Frontend\User\Form;

use Zend\Form\Fieldset;

class UserDetailsFieldset extends Fieldset
{
    public function __construct($name = 'user_details', array $options = [])
    {
        parent::__construct($name, $options);
        $this->init();
    }

    public function init()
    {
        $this->add([
            'type' => 'text',
            'name' => 'firstName',
            'options' => [
                'label' => 'First Name',
            ],
            'attributes' => [
                'placeholder' => 'First Name'
            ]
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'lastName',
            'options' => [
                'label' => 'Last Name'
            ],
            'attributes' => [
                'placeholder' => 'Last Name'
            ]
        ]);
    }
}