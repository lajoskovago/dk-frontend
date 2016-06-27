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

    
}