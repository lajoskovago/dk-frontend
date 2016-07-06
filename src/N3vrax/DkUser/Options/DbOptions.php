<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/6/2016
 * Time: 7:39 PM
 */

namespace N3vrax\DkUser\Options;

use Zend\Stdlib\AbstractOptions;

class DbOptions extends AbstractOptions
{
    /** @var  string */
    protected $dbAdapter;

    /** @var  string */
    protected $userTable;

    /** @var  string */
    protected $userResetTokenTable;

    /** @var  string */
    protected $userConfirmTokenTable;

    /**
     * @return string
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @param string $dbAdapter
     * @return DbOptions
     */
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserTable()
    {
        return $this->userTable;
    }

    /**
     * @param string $userTable
     * @return DbOptions
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
     * @return DbOptions
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
     * @return DbOptions
     */
    public function setUserConfirmTokenTable($userConfirmTokenTable)
    {
        $this->userConfirmTokenTable = $userConfirmTokenTable;
        return $this;
    }
    
}