<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/23/2016
 * Time: 3:44 PM
 */

namespace N3vrax\DkUser\Options;

use Zend\Stdlib\AbstractOptions;

class LoginOptions extends AbstractOptions
{
    /** @var bool  */
    protected $enableRememberMe = true;

    /** @var array  */
    protected $authIdentityFields = ['username', 'email'];

    /** @var bool  */
    protected $__strictMode__ = false;

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
        $this->authIdentityFields = $authIdentityFields;
        return $this;
    }



}