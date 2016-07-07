<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/7/2016
 * Time: 9:05 PM
 */

namespace N3vrax\DkUser\Mapper;

interface MapperInterface
{
    public function lastInsertValue();

    public function beginTransaction();

    public function commit();

    public function rollback();
}