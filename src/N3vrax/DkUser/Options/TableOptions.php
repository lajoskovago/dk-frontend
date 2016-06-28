<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/27/2016
 * Time: 5:28 PM
 */

namespace N3vrax\DkUser\Options;

use Zend\Stdlib\AbstractOptions;

class TableOptions extends AbstractOptions
{
    protected $userTable = 'user';

    protected $userResetTokenTable = 'user_reset_token';

    protected $userConfirmTokenTable = 'user_confirm_token';

    /**
     * @return string
     */
    public function getUserTable()
    {
        return $this->userTable;
    }

    /**
     * @param string $userTable
     * @return TableOptions
     */
    public function setUserTable($userTable)
    {
        $this->userTable = $userTable;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserResetTokenTable()
    {
        return $this->userResetTokenTable;
    }

    /**
     * @param string $userResetTokenTable
     * @return TableOptions
     */
    public function setUserResetTokenTable($userResetTokenTable)
    {
        $this->userResetTokenTable = $userResetTokenTable;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserConfirmTokenTable()
    {
        return $this->userConfirmTokenTable;
    }

    /**
     * @param string $userConfirmTokenTable
     * @return TableOptions
     */
    public function setUserConfirmTokenTable($userConfirmTokenTable)
    {
        $this->userConfirmTokenTable = $userConfirmTokenTable;
        return $this;
    }

    
    
}