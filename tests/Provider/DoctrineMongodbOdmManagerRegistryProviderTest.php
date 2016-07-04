<?php

namespace Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Provider;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\SchemaManager;
use Pimple\Container;
use Saxulum\DoctrineMongoDb\Provider\DoctrineMongoDbProvider;
use Saxulum\DoctrineMongoDbOdm\Provider\DoctrineMongoDbOdmProvider;
use Saxulum\DoctrineMongodbOdmManagerRegistry\Provider\DoctrineMongodbOdmManagerRegistryProvider;
use Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Document\SampleDocument;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Validator\Validator;

class DoctrineMongodbOdmManagerRegistryProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSchema()
    {
        $container = $this->getContainer();

        /** @var DocumentManager $dm */
        $dm = $container['doctrine_mongodb']->getManager();

        $schemaManager = $this->getSchemaManager($dm);

        $this->createSchema($schemaManager);
        $this->dropSchema($schemaManager);
    }

    public function testValidator()
    {
        $container = $this->getContainer();

        /** @var DocumentManager $dm */
        $dm = $container['doctrine_mongodb']->getManager();

        /** @var Validator $validator */
        $validator = $container['validator'];

        $schemaManager = $this->getSchemaManager($dm);

        $this->createSchema($schemaManager);

        $sampleDocument = new SampleDocument();
        $sampleDocument->setName('name');

        $errors = $validator->validate($sampleDocument);

        $this->assertCount(0, $errors);

        $dm->persist($sampleDocument);
        $dm->flush();

        $sampleDocument = new SampleDocument();
        $sampleDocument->setName('name');

        $errors = $validator->validate($sampleDocument);

        $this->assertCount(1, $errors);

        $this->dropSchema($schemaManager);
    }

    public function getContainer()
    {
        $container = new Container();
        $container['debug'] = true;

        $container->register(new ValidatorServiceProvider());
        $container->register(new DoctrineMongoDbProvider(), array(
            'mongodb.options' => array(
                'server' => 'mongodb://localhost:27017',
//                'options' => array(
//                    'username' => 'root',
//                    'password' => 'root',
//                    'db' => 'admin'
//                )
            )
        ));
        $container->register(new DoctrineMongoDbOdmProvider(), array(
            "mongodbodm.proxies_dir" => $this->getCacheDir() . '/doctrine/proxies',
            "mongodbodm.hydrator_dir" => $this->getCacheDir() . '/doctrine/hydrator',
            'mongodbodm.dm.options' => array(
                'mappings' => array(
                    array(
                        'type' => 'annotation',
                        'namespace' => 'Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Document',
                        'path' => __DIR__.'/../Document',
                        'use_simple_annotation_reader' => false,
                    )
                )
            )
        ));
        $container->register(new DoctrineMongodbOdmManagerRegistryProvider());

        return $container;
    }

    /**
     * @param SchemaManager $schemaManager
     */
    protected function createSchema(SchemaManager $schemaManager)
    {
        $schemaManager->createCollections();
    }

    /**
     * @param SchemaManager $schemaManager
     */
    protected function dropSchema(SchemaManager $schemaManager)
    {
        $schemaManager->dropCollections();
    }

    /**
     * @param  DocumentManager $dm
     * @return SchemaManager
     */
    protected function getSchemaManager(DocumentManager $dm)
    {
        return new SchemaManager($dm, $dm->getMetadataFactory());
    }

    /**
     * @return string
     */
    protected function getCacheDir()
    {
        $cacheDir =  __DIR__ . '/../cache';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        return $cacheDir;
    }
}
