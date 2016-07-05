<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/5/2016
 * Time: 7:46 PM
 */

namespace N3vrax\DkMail\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkMail\Controller\Plugin\SendMailPlugin;
use N3vrax\DkMail\Factory\AbstractMailFactory;
use N3vrax\DkMail\Factory\MailServiceAbstractFactory;
use N3vrax\DkMail\Service\MailServiceInterface;
use Zend\Stdlib\StringUtils;

class SendMailPluginAbstractFactory extends AbstractMailFactory
{
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if(strpos($requestedName, 'sendMail') !== 0) {
            return false;
        }

        if($requestedName === 'sendMail') {
            return true;
        }

        $specificServiceName = $this->getSpecificServiceName($requestedName);
        return array_key_exists($specificServiceName, $this->getConfig($container));
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $specificServiceName = $this->getSpecificServiceName($requestedName);

        /** @var MailServiceInterface $mailService */
        $mailService = $container->get(sprintf(
            '%s.%s.%s', self::DKMAIL_PART, MailServiceAbstractFactory::SPECIFIC_PART, $specificServiceName
        ));

        return new SendMailPlugin($mailService);
    }

    protected function getSpecificServiceName($requestedName)
    {
        $parts = explode('_', $this->camelCaseToUnderscore($requestedName));
        if(count($parts) === 2) {
            return 'default';
        }

        //discard the sendMail part
        $parts = array_slice($parts, 2);
        $specificServiceName = '';
        foreach ($parts as $part) {
            $specificServiceName .= $part;
        }

        //convert from camecase to underscores and set to lower
        return strtolower($specificServiceName);
    }

    protected function camelCaseToUnderscore($value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            return $value;
        }

        if (StringUtils::hasPcreUnicodeSupport()) {
            $pattern     = ['#(?<=(?:\p{Lu}))(\p{Lu}\p{Ll})#', '#(?<=(?:\p{Ll}|\p{Nd}))(\p{Lu})#'];
            $replacement = ['_\1', '_\1'];
        }
        else {
            $pattern     = ['#(?<=(?:[A-Z]))([A-Z]+)([A-Z][a-z])#', '#(?<=(?:[a-z0-9]))([A-Z])#'];
            $replacement = ['\1_\2', '_\1'];
        }

        return preg_replace($pattern, $replacement, $value);
    }
}