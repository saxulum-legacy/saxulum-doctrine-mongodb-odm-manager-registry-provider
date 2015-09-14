<?php

namespace Saxulum\DoctrineMongodbOdmManagerRegistry\Cilex\Provider;

use Saxulum\DoctrineMongodbOdmManagerRegistry\Provider\DoctrineMongodbOdmManagerRegistryProvider as PimpleDoctrineMongodbOdmManagerRegistryProvider;
use Cilex\Application;
use Cilex\ServiceProviderInterface;

class DoctrineMongodbOdmManagerRegistryProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $pimpleServiceProvider = new PimpleDoctrineMongodbOdmManagerRegistryProvider;
        $pimpleServiceProvider->register($app);
    }
}
