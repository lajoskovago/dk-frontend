<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/9/2016
 * Time: 9:28 PM
 */

namespace Frontend\Twig;

use N3vrax\DkAuthentication\AuthenticationInterface;

class AuthenticationExtension extends \Twig_Extension
{
    /** @var AuthenticationInterface  */
    protected $authentication;

    /**
     * AuthenticationExtension constructor.
     * @param AuthenticationInterface $authentication
     */
    public function __construct(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dk-authentication';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('hasIdentity', [$this, 'hasIdentity']),
            new \Twig_SimpleFunction('getIdentity', [$this, 'getIdentity']),
        ];
    }

    public function hasIdentity()
    {
        return $this->authentication->hasIdentity();
    }

    public function getIdentity()
    {
        return $this->authentication->getIdentity();
    }

}