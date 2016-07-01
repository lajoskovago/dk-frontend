<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/1/2016
 * Time: 8:28 PM
 */

namespace N3vrax\DkMail\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkMail\Options\MailOptions;
use Zend\Stdlib\ArrayUtils;

class MailOptionsAbstractFactory extends AbstractMailFactory
{
    const SPECIFIC_PART = 'mailoptions';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $specificServiceName = explode('.', $requestedName)[2];
        $config = $this->getConfig($container);
        $specificConfig = $config[$specificServiceName];
        if(!is_array($specificConfig)) {
            $specificConfig = [];
        }

        do {
            $extendsConfigKey = isset($specificConfig['extends']) && is_string($specificConfig['extends'])
                ? trim($specificConfig['extends'])
                : null;

            unset($specificConfig['extends']);

            if(!is_null($extendsConfigKey)
                && array_key_exists($extendsConfigKey, $config)
                && is_array($config[$extendsConfigKey])
            ) {
                $specificConfig = ArrayUtils::merge($config[$extendsConfigKey], $specificConfig);
            }

        } while($extendsConfigKey != null);

        return new MailOptions($specificConfig);
    }
}