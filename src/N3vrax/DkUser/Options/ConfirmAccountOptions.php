<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/6/2016
 * Time: 8:17 PM
 */

namespace N3vrax\DkUser\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

class ConfirmAccountOptions extends AbstractOptions
{
    const MESSAGE_CONFIRM_ACCOUNT_MISSING_PARAMS = 1;
    const MESSAGE_CONFIRM_ACCOUNT_INVALID_EMAIL = 2;
    const MESSAGE_CONFIRM_ACCOUNT_INVALID_TOKEN = 3;
    const MESSAGE_CONFIRM_ACCOUNT_DISABLED = 4;
    const MESSAGE_CONFIRM_ACCOUNT_ERROR = 5;
    const MESSAGE_CONFIRM_ACCOUNT_SUCCESS = 6;

    /** @var bool  */
    protected $enableAccountConfirmation = true;

    /** @var array  */
    protected $messages = [
        ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_DISABLED => 'Account confirmation is disabled',
        ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_ERROR => 'Account confirmation error. Please try again',
        ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_EMAIL => 'Account confirmation invalid parameters',
        ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_TOKEN => 'Account confirmation invalid parameters',
        ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_MISSING_PARAMS => 'Account confirmation invalid parameters',
        ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_SUCCESS => 'Account successfully confirmed. You may sign in now',
    ];

    /**
     * @return boolean
     */
    public function isEnableAccountConfirmation()
    {
        return $this->enableAccountConfirmation;
    }

    /**
     * @param boolean $enableAccountConfirmation
     * @return ConfirmAccountOptions
     */
    public function setEnableAccountConfirmation($enableAccountConfirmation)
    {
        $this->enableAccountConfirmation = $enableAccountConfirmation;
        return $this;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     * @return ConfirmAccountOptions
     */
    public function setMessages($messages)
    {
        $this->messages = ArrayUtils::merge($this->messages, $messages, true);
        return $this;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public function getMessage($key)
    {
        return isset($this->messages[$key]) ? $this->messages[$key] : 'Unknown message';
    }

}