<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/8/2016
 * Time: 6:54 PM
 */

namespace Frontend\Factory;

use Frontend\Authentication\WebPreAuthenticationListener;
use Frontend\Form\LoginForm;
use Interop\Container\ContainerInterface;
use N3vrax\DkWebAuthentication\Options\ModuleOptions;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class WebPreAuthenticationListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new WebPreAuthenticationListener(
            $container->get(TemplateRendererInterface::class),
            $container->get(LoginForm::class),
            $container->get(UrlHelper::class),
            $container->get(ModuleOptions::class));
    }
}