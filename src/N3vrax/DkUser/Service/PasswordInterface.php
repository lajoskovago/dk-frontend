<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/24/2016
 * Time: 7:25 PM
 */

namespace N3vrax\DkUser\Service;

interface PasswordInterface
{
    public function create($password);

    public function verify($hash, $password);

    public function needsRehash($hash);

    public function getInfo($hash);
}