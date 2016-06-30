<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/23/2016
 * Time: 3:50 PM
 */

namespace N3vrax\DkUser\Factory\Form;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\DIGetEventManagerTrait;
use N3vrax\DkUser\Form\InputFilter\LoginInputFilter;
use N3vrax\DkUser\Form\LoginForm;
use N3vrax\DkUser\Options\LoginOptions;
use N3vrax\DkUser\Options\ModuleOptions;

class LoginFormFactory
{
    use DIGetEventManagerTrait;

    public function __invoke(ContainerInterface $container)
    {
        $moduleOptions = $container->get(ModuleOptions::class);
        $loginOptions = $container->get(LoginOptions::class);
        
        $filter = new LoginInputFilter($moduleOptions, $loginOptions);
        $filter->setEventManager($this->getEventManager($container));
        
        $form = new LoginForm($loginOptions);
        $form->setInputFilter($filter);
        $form->setEventManager($this->getEventManager($container));

        return $form;
    }
}