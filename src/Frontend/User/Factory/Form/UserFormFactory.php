<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/5/2016
 * Time: 7:36 PM
 */

namespace Frontend\User\Factory\Form;

use Frontend\User\Entity\UserEntityHydrator;
use Frontend\User\Form\InputFilter\UserDetailsInputFilter;
use Frontend\User\Form\InputFilter\UserInputFilter;
use Frontend\User\Form\UserForm;
use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Validator\NoRecordsExists;

class UserFormFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $form = new UserForm();
        $form->init();

        $userFilter = new UserInputFilter(
            $container->get(UserOptions::class),
            new NoRecordsExists([
                'mapper' => $container->get(UserMapperInterface::class),
                'key' => 'username',
            ]),
            new UserDetailsInputFilter()
        );
        $userFilter->init();

        $form->setInputFilter($userFilter);
        $form->setHydrator(new UserEntityHydrator());

        return $form;
    }
}