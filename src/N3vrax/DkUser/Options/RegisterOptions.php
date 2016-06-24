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
    protected $enableRegistration = true;

    protected $defaultUserStatus;

    protected $__strictMode__ = false;

    /**
     * @return boolean
     */
    public function isEnableRegistration()
    {
        return $this->enableRegistration;
    }

    /**
     * @param boolean $enableRegistration
     * @return RegisterOptions
     */
    public function setEnableRegistration($enableRegistration)
    {
        $this->enableRegistration = $enableRegistration;
        return $this;
    }

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


}