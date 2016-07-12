<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/23/2016
 * Time: 3:44 PM
 */

namespace N3vrax\DkUser\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

class LoginOptions extends AbstractOptions
{
    const MESSAGE_LOGIN_EMPTY_IDENTITY = 1;
    const MESSAGE_LOGIN_EMPTY_PASSWORD = 2;
    const MESSAGE_LOGIN_PASSWORD_TOO_SHORT = 3;
    const MESSAGE_LOGIN_ACCOUNT_INACTIVE = 4;

    /** @var bool  */
    protected $enableRememberMe = true;

    /** @var string  */
    protected $rememberMeCookieName = 'rememberMe';

    /** @var int  */
    protected $rememberMeCookieExpire = 60*60*24*30;

    /** @var bool  */
    protected $rememberMeCookieSecure = false;

    /** @var array  */
    protected $authIdentityFields = ['username', 'email'];

    /** @var  array */
    protected $allowedLoginStatuses = ['active'];

    /** @var int  */
    protected $loginFormTimeout = 1800;

    /** @var array  */
    protected $messages = [
        LoginOptions::MESSAGE_LOGIN_EMPTY_IDENTITY => 'Identity is required and cannot be empty',
        LoginOptions::MESSAGE_LOGIN_EMPTY_PASSWORD => 'Password is required and cannot be empty',
        LoginOptions::MESSAGE_LOGIN_PASSWORD_TOO_SHORT => 'Password must have at least 4 characters',
        LoginOptions::MESSAGE_LOGIN_ACCOUNT_INACTIVE => 'Account is not active or it has not been confirmed',
    ];

    /**
     * @return boolean
     */
    public function isEnableRememberMe()
    {
        return $this->enableRememberMe;
    }

    /**
     * @param boolean $enableRememberMe
     * @return LoginOptions
     */
    public function setEnableRememberMe($enableRememberMe)
    {
        $this->enableRememberMe = $enableRememberMe;
        return $this;
    }

    /**
     * @return array
     */
    public function getAuthIdentityFields()
    {
        return $this->authIdentityFields;
    }

    /**
     * @param array $authIdentityFields
     * @return LoginOptions
     */
    public function setAuthIdentityFields($authIdentityFields)
    {
        $this->authIdentityFields = (array) $authIdentityFields;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedLoginStatuses()
    {
        return $this->allowedLoginStatuses;
    }

    /**
     * @param array $allowedLoginStatuses
     * @return LoginOptions
     */
    public function setAllowedLoginStatuses($allowedLoginStatuses)
    {
        $this->allowedLoginStatuses = $allowedLoginStatuses;
        return $this;
    }

    /**
     * @return int
     */
    public function getLoginFormTimeout()
    {
        return $this->loginFormTimeout;
    }

    /**
     * @param int $loginFormTimeout
     * @return LoginOptions
     */
    public function setLoginFormTimeout($loginFormTimeout)
    {
        $this->loginFormTimeout = $loginFormTimeout;
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
     * @return LoginOptions
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

    /**
     * @return string
     */
    public function getRememberMeCookieName()
    {
        return $this->rememberMeCookieName;
    }

    /**
     * @param string $rememberMeCookieName
     * @return LoginOptions
     */
    public function setRememberMeCookieName($rememberMeCookieName)
    {
        $this->rememberMeCookieName = $rememberMeCookieName;
        return $this;
    }

    /**
     * @return int
     */
    public function getRememberMeCookieExpire()
    {
        return $this->rememberMeCookieExpire;
    }

    /**
     * @param int $rememberMeCookieExpire
     * @return LoginOptions
     */
    public function setRememberMeCookieExpire($rememberMeCookieExpire)
    {
        $this->rememberMeCookieExpire = $rememberMeCookieExpire;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRememberMeCookieSecure()
    {
        return $this->rememberMeCookieSecure;
    }

    /**
     * @param boolean $rememberMeCookieSecure
     * @return LoginOptions
     */
    public function setRememberMeCookieSecure($rememberMeCookieSecure)
    {
        $this->rememberMeCookieSecure = $rememberMeCookieSecure;
        return $this;
    }


}