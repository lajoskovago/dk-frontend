<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/1/2016
 * Time: 7:15 PM
 */

namespace N3vrax\DkMail\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

abstract class AbstractMailFactory implements AbstractFactoryInterface
{
    const DKMAIL_PART = 'dkmail';
    const SPECIFIC_PART = '';

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $parts = explode('.', $requestedName);
        if(count($parts) !== 3) {
            return false;
        }

        if($parts[0] !== self::DKMAIL_PART || $parts[1] !== static::SPECIFIC_PART) {
            return false;
        }

        $specificServiceName = $parts[2];
        $config = $this->getConfig($container);
        return array_key_exists($specificServiceName, $config);
    }

    protected function getConfig(ContainerInterface $container)
    {
        $config = $container->get('config');
        if(isset($config['dk_mail']) && is_array($config['dk_mail'])) {
            return $config['dk_mail'];
        }

        return [];
    }
}