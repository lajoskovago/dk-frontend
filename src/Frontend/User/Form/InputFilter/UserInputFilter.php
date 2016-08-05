<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/4/2016
 * Time: 11:38 PM
 */

namespace Frontend\User\Form\InputFilter;

use N3vrax\DkUser\Options\UserOptions;
use Zend\InputFilter\InputFilter;

class UserInputFilter extends InputFilter
{
    /** @var  UserOptions */
    protected $userOptions;

    protected $usernameValidator;

    /** @var  InputFilter */
    protected $userDetailsInputFilter;

    /**
     * UserInputFilter constructor.
     * @param UserOptions $userOptions
     * @param $usernameValidator
     * @param InputFilter $userDetailsInputFilter
     */
    public function __construct(
        UserOptions $userOptions,
        $usernameValidator,
        InputFilter $userDetailsInputFilter
    )
    {
        $this->userOptions = $userOptions;
        $this->usernameValidator = $usernameValidator;
        $this->userDetailsInputFilter = $userDetailsInputFilter;
    }

    public function init()
    {
        if($this->userOptions->getRegisterOptions()->isEnableUsername()) {
            $this->add([
                'name' => 'username',
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => 'Username is required and cannot be empty'
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 255,
                            'message' => 'Username invalid length - must be between 3 and 255 characters'
                        ]
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[a-zA-Z0-9-_]+$/',
                            'message' => 'Username invalid characters - only digits, letters and underscore allowed'
                        ]
                    ],
                    $this->usernameValidator,
                ],
            ]);
        }

        $this->add($this->userDetailsInputFilter, 'details');
    }
}