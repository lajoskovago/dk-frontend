<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 7:55 PM
 */

namespace N3vrax\DkUser\Factory\Form;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\DIGetEventManagerTrait;
use N3vrax\DkUser\Form\InputFilter\RegisterInputFilter;
use N3vrax\DkUser\Form\RegisterForm;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use N3vrax\DkUser\Validator\NoRecordsExists;

class RegisterFormFactory
{
    use DIGetEventManagerTrait;

    public function __invoke(ContainerInterface $container)
    {
        $registerOptions = $container->get(RegisterOptions::class);
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        $filter = new RegisterInputFilter(
            $moduleOptions,
            $registerOptions,
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

        $form = new RegisterForm($registerOptions);
        $form->setInputFilter($filter);
        $form->setHydrator($container->get($moduleOptions->getUserEntityHydrator()));
        $form->setEventManager($this->getEventManager($container));

        return $form;
    }
}