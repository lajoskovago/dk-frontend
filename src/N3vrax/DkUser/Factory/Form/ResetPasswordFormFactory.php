<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/26/2016
 * Time: 9:06 PM
 */

namespace N3vrax\DkUser\Factory\Form;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\DIGetEventManagerTrait;
use N3vrax\DkUser\Form\InputFilter\ResetPasswordInputFilter;
use N3vrax\DkUser\Form\ResetPasswordForm;

class ResetPasswordFormFactory
{
    use DIGetEventManagerTrait;

    public function __invoke(ContainerInterface $container)
    {
        $filter = new ResetPasswordInputFilter();
        $filter->setEventManager($this->getEventManager($container));

        $form = new ResetPasswordForm();
        $form->setInputFilter($filter);
        $form->setEventManager($this->getEventManager($container));

        return $form;
    }
}