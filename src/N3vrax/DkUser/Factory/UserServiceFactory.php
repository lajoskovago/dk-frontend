<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:59 PM
 */

namespace N3vrax\DkUser\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Event\Listener\UserListenerAwareInterface;
use N3vrax\DkUser\Event\Listener\UserListenerInterface;
use N3vrax\DkUser\Exception\InvalidArgumentException;
use N3vrax\DkUser\Form\RegisterForm;
use N3vrax\DkUser\Form\ResetPasswordForm;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Service\PasswordInterface;
use N3vrax\DkUser\Service\UserService;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class UserServiceFactory
{
    /** @var  UserOptions */
    protected $options;

    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);
        $this->options = $options;

        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $service = new UserService(
            $container->get(UserMapperInterface::class),
            $options,
            $container->get(RegisterForm::class),
            $container->get(ResetPasswordForm::class),
            $container->get($options->getUserEntityClass()),
            $container->get(PasswordInterface::class)
        );

        $service->setEventManager($eventManager);

        $this->attachUserListeners($service, $container);

        return $service;
    }

    protected function attachUserListeners(UserListenerAwareInterface $service, ContainerInterface $container)
    {
        $listeners = $this->options->getUserListeners();
        foreach ($listeners as $listener) {
            if (is_string($listener) && $container->has($listener)) {
                $listener = $container->get($listener);
            } elseif (is_string($listener) && class_exists($listener)) {
                $listener = new $listener;
            }

            if (!$listener instanceof UserListenerInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Provided mail listener of type "%s" is not valid. Expected string or %s',
                    is_object($listener) ? get_class($listener) : gettype($listener),
                    UserListenerInterface::class
                ));
            }

            $service->attachUserListener($listener);
        }
    }
}