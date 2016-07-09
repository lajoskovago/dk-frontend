<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 7:55 PM
 */

namespace N3vrax\DkUser\Factory\Form;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\DiGetEventManagerTrait;
use N3vrax\DkUser\Form\InputFilter\RegisterInputFilter;
use N3vrax\DkUser\Form\RegisterForm;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Validator\NoRecordsExists;

class RegisterFormFactory
{
    use DiGetEventManagerTrait;

    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $moduleOptions */
        $options = $container->get(UserOptions::class);

        $filter = new RegisterInputFilter(
            $options,
            new NoRecordsExists([
                'mapper' => $container->get(UserMapperInterface::class),
                'key' => 'email'
            ]),
            new NoRecordsExists([
                'mapper' => $container->get(UserMapperInterface::class),
                'key' => 'username'
            ])
        );
        $filter->setEventManager($this->getEventManager($container));

        $form = new RegisterForm($options);
        $form->setInputFilter($filter);
        $form->setHydrator($container->get($options->getUserEntityHydrator()));
        $form->setEventManager($this->getEventManager($container));

        return $form;
    }
}