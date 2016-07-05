<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/5/2016
 * Time: 7:54 PM
 */

namespace N3vrax\DkMail;

use N3vrax\DkMail\Controller\Plugin\Factory\SendMailPluginAbstractFactory;
use N3vrax\DkMail\Factory\MailOptionsAbstractFactory;
use N3vrax\DkMail\Factory\MailServiceAbstractFactory;

class ConfigProvider
{
    public function __invoke()
    {
        return [

            'dependencies' => [
                'abstract_factories' => [
                    MailServiceAbstractFactory::class,
                    MailOptionsAbstractFactory::class,
                ]
            ],

            'dk_controller' => [

                'plugin_manager' => [
                    'abstract_factories' => [
                        SendMailPluginAbstractFactory::class,
                    ]
                ]

            ],

            'dk_mail' => [

            ]

        ];
    }
}