<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/9/2016
 * Time: 11:39 PM
 */

namespace Frontend\Twig;

use N3vrax\DkAuthorization\AuthorizationInterface;

class AuthorizationExtension extends \Twig_Extension
{
    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    public function __construct(AuthorizationInterface $authorization)
    {
        $this->authorization = $authorization;
    }

    public function getName()
    {
        return 'dk-authorization';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('isGranted', [$this, 'isGranted']),
        ];
    }

    /**
     * @param $permission
     * @param array $roles
     * @param null $context
     * @return bool
     */
    public function isGranted($permission, array $roles = [], $context = null)
    {
        return $this->authorization->isGranted($permission, $roles, $context);
    }

}