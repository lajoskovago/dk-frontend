<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/27/2016
 * Time: 5:29 PM
 */

namespace N3vrax\DkUser\Factory\Options;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Options\TableOptions;

class TableOptionsFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new TableOptions($container->get('config')['dk_user']['table_settings']);
    }
}