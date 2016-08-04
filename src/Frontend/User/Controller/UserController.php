<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Frontend\User\Controller;

use N3vrax\DkBase\Controller\AbstractActionController;
use Zend\Diactoros\Response\HtmlResponse;

class UserController extends AbstractActionController
{
    
    public function accountAction()
    {
        return new HtmlResponse($this->template()->render('app::account'));
    }
}