<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/23/2016
 * Time: 3:50 PM
 */

namespace N3vrax\DkUser\Factory\Form;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\DiGetEventManagerTrait;
use N3vrax\DkUser\Form\InputFilter\LoginInputFilter;
use N3vrax\DkUser\Form\LoginForm;
use N3vrax\DkUser\Options\UserOptions;

class LoginFormFactory
{
    use DiGetEventManagerTrait;

    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(UserOptions::class);

        $filter = new LoginInputFilter($options);
        $filter->setEventManager($this->getEventManager($container));
        
        $form = new LoginForm($options);
        $form->setInputFilter($filter);
        $form->setEventManager($this->getEventManager($container));

        return $form;
    }
}