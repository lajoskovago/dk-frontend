<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/8/2016
 * Time: 6:54 PM
 */

namespace Frontend\Factory;

use Frontend\Authentication\PreAuthCallback;
use Frontend\Form\LoginForm;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class PreAuthCallbackFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new PreAuthCallback(
            $container->get(TemplateRendererInterface::class),
            $container->get(LoginForm::class),
            $container->get(UrlHelper::class));
    }
}