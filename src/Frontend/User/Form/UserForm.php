<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/4/2016
 * Time: 11:37 PM
 */

namespace Frontend\User\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = 'user', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add([
            'name' => 'username',
            'type' => 'text',
            'options' => [
                'label' => 'Username'
            ],
            'attributes' => [
                'placeholder' => 'Username'
            ]
        ]);

        $detailsFieldset = new UserDetailsFieldset();
        $detailsFieldset->setName('details');

        $this->add($detailsFieldset);

        $this->add(new Csrf('update_user_csrf'));

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Update account'
            ], ['priority' => -100]
        ]);
    }
}