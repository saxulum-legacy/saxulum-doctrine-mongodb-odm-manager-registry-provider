<?php

namespace Saxulum\DoctrineMongodbOdmManagerRegistry\Provider;

use Saxulum\Console\Console\ConsoleApplication;
use Saxulum\DoctrineMongodbOdmCommands\Command\ClearMetadataCacheDoctrineODMCommand;
use Saxulum\DoctrineMongodbOdmCommands\Command\CreateSchemaDoctrineODMCommand;
use Saxulum\DoctrineMongodbOdmCommands\Command\DropSchemaDoctrineODMCommand;
use Saxulum\DoctrineMongodbOdmCommands\Command\GenerateHydratorsDoctrineODMCommand;
use Saxulum\DoctrineMongodbOdmCommands\Command\GenerateProxiesDoctrineODMCommand;
use Saxulum\DoctrineMongodbOdmCommands\Command\InfoDoctrineODMCommand;
use Saxulum\DoctrineMongodbOdmCommands\Command\QueryDoctrineODMCommand;
use Saxulum\DoctrineMongodbOdmCommands\Command\UpdateSchemaDoctrineODMCommand;
use Saxulum\DoctrineMongodbOdmCommands\Helper\ManagerRegistryHelper;
use Saxulum\DoctrineMongodbOdmManagerRegistry\Doctrine\ManagerRegistry;
use Saxulum\DoctrineMongodbOdmManagerRegistry\Form\DoctrineMongoDBExtension;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use Symfony\Bridge\Doctrine\Validator\DoctrineInitializer;

class DoctrineMongodbOdmManagerRegistryProvider
{
    public function register(\Pimple $container)
    {
        $container['doctrine_mongodb'] = $container->share(function ($container) {
            return new ManagerRegistry($container);
        });

        if (isset($container['form.extensions'])) {
            $container['form.extensions'] = $container->extend('form.extensions', function ($extensions, $container) {
                $extensions[] = new DoctrineMongoDBExtension($container['doctrine_mongodb']);

                return $extensions;
            });
        }

        if (isset($container['validator']) &&  class_exists('Symfony\\Bridge\\Doctrine\\Validator\\Constraints\\UniqueEntityValidator')) {
            $container['doctrine.orm.validator.unique_validator'] = function ($container) {
                return new UniqueEntityValidator($container['doctrine_mongodb']);
            };

            if (!isset($container['validator.validator_service_ids'])) {
                $container['validator.validator_service_ids'] = array();
            }

            $container['validator.validator_service_ids'] = array_merge(
                $container['validator.validator_service_ids'],
                array('doctrine_odm.mongodb.unique' => 'doctrine.orm.validator.unique_validator')
            );

            $container['validator.object_initializers'] = $container->extend('validator.object_initializers',
                function (array $objectInitializers) use ($container) {
                    $objectInitializers[] = new DoctrineInitializer($container['doctrine_mongodb']);

                    return $objectInitializers;
                }
            );
        }

        if (class_exists('Saxulum\\DoctrineOrmCommands\\Command\\CreateDatabaseDoctrineCommand')) {
            if (isset($container['console'])) {
                $container['console'] = $container->extend('console', function (ConsoleApplication $consoleApplication) use ($container) {
                    $helperSet = $consoleApplication->getHelperSet();
                    $helperSet->set(new ManagerRegistryHelper($container['doctrine_mongodb']), 'doctrine_mongodb');

                    return $consoleApplication;
                });
            }

            if (isset($container['console.commands'])) {
                $container['console.commands'] = $container->extend('console.commands', function ($commands) use ($container) {
                    $commands[] = new CreateSchemaDoctrineODMCommand;
                    $commands[] = new UpdateSchemaDoctrineODMCommand;
                    $commands[] = new DropSchemaDoctrineODMCommand;
                    $commands[] = new QueryDoctrineODMCommand;
                    $commands[] = new ClearMetadataCacheDoctrineODMCommand;
                    $commands[] = new GenerateHydratorsDoctrineODMCommand;
                    $commands[] = new GenerateProxiesDoctrineODMCommand;
                    $commands[] = new InfoDoctrineODMCommand;

                    return $commands;
                });
            }
        }
    }
}
