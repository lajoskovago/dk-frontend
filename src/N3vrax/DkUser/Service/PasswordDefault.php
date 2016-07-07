<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/24/2016
 * Time: 7:31 PM
 */

namespace N3vrax\DkUser\Service;


use N3vrax\DkUser\Options\UserOptions;

class PasswordDefault implements PasswordInterface
{
    /** @var  UserOptions */
    protected $options;

    public function __construct(UserOptions $options)
    {
        $this->options = $options;
    }

    /**
     * This class act as a functor for password verification
     * can be used where a passowrd validation callback is needed
     *
     * @param $hash
     * @param $password
     */
    public function __invoke($hash, $password)
    {
        return $this->verify($hash, $password);
    }

    public function create($password)
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $this->options->getPasswordCost()]);
    }

    public function verify($hash, $password)
    {
        return password_verify($password, $hash);
    }

    public function needsRehash($hash)
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => $this->options->getPasswordCost()]);
    }

    public function getInfo($hash)
    {
        return password_get_info($hash);
    }

}