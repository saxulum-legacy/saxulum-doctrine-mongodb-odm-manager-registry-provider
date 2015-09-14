<?php

namespace Saxulum\DoctrineMongodbOdmManagerRegistry\Provider;

use Saxulum\DoctrineMongodbOdmManagerRegistry\Doctrine\ManagerRegistry;

class DoctrineMongodbOdmManagerRegistryProvider
{
    public function register(\Pimple $container)
    {
        $container['doctrine_mongodb'] = $container->share(function ($container) {
            return new ManagerRegistry($container);
        });
    }
}
