<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/10/2016
 * Time: 7:22 PM
 */

namespace Frontend\Controller;

use N3vrax\DkBase\Controller\AbstractActionController;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

class PageController extends AbstractActionController
{
    public function indexAction()
    {
        return new RedirectResponse($this->urlHelper()->generate('home'));
    }

    public function aboutUsAction()
    {
        return new HtmlResponse($this->template()->render('page::about-us'));
    }

    public function whoWeAreAction()
    {
        return new HtmlResponse($this->template()->render('page::who-we-are'));
    }
}