<?php

namespace Saxulum\DoctrineMongodbOdmManagerRegistry\Silex\Provider;

use Saxulum\DoctrineMongodbOdmManagerRegistry\Provider\DoctrineMongodbOdmManagerRegistryProvider as PimpleDoctrineMongodbOdmManagerRegistryProvider;
use Silex\Application;
use Silex\ServiceProviderInterface;

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
