<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 7:48 PM
 */

namespace N3vrax\DkUser\Options;

use Zend\Stdlib\AbstractOptions;

class RegisterOptions extends AbstractOptions
{
    protected $enableUsername = true;

    protected $defaultUserStatus;

    protected $userFormTimeout = 300;

    protected $useRegistrationFormCaptcha = true;

    protected $formCaptchaOptions;

    protected $__strictMode__ = false;
    

    /**
     * @return mixed
     */
    public function getDefaultUserStatus()
    {
        return $this->defaultUserStatus;
    }

    /**
     * @param mixed $defaultUserStatus
     * @return RegisterOptions
     */
    public function setDefaultUserStatus($defaultUserStatus)
    {
        $this->defaultUserStatus = $defaultUserStatus;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnableUsername()
    {
        return $this->enableUsername;
    }

    /**
     * @param boolean $enableUsername
     * @return RegisterOptions
     */
    public function setEnableUsername($enableUsername)
    {
        $this->enableUsername = $enableUsername;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isUseRegistrationFormCaptcha()
    {
        return $this->useRegistrationFormCaptcha;
    }

    /**
     * @param boolean $useRegistrationFormCaptcha
     * @return RegisterOptions
     */
    public function setUseRegistrationFormCaptcha($useRegistrationFormCaptcha)
    {
        $this->useRegistrationFormCaptcha = $useRegistrationFormCaptcha;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormCaptchaOptions()
    {
        return $this->formCaptchaOptions;
    }

    /**
     * @param mixed $formCaptchaOptions
     * @return RegisterOptions
     */
    public function setFormCaptchaOptions($formCaptchaOptions)
    {
        $this->formCaptchaOptions = $formCaptchaOptions;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserFormTimeout()
    {
        return $this->userFormTimeout;
    }

    /**
     * @param int $userFormTimeout
     * @return RegisterOptions
     */
    public function setUserFormTimeout($userFormTimeout)
    {
        $this->userFormTimeout = $userFormTimeout;
        return $this;
    }

    
}