<?php

namespace Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Provider;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\SchemaManager;
use Saxulum\DoctrineMongoDb\Silex\Provider\DoctrineMongoDbProvider;
use Saxulum\DoctrineMongoDbOdm\Silex\Provider\DoctrineMongoDbOdmProvider;
use Saxulum\DoctrineMongodbOdmManagerRegistry\Silex\Provider\DoctrineMongodbOdmManagerRegistryProvider;
use Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Document\SampleDocument;
use Silex\Application;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Validator\Validator;

class DoctrineMongodbOdmManagerRegistryProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSchema()
    {
        $app = $this->createApplication();

        /** @var DocumentManager $dm */
        $dm = $app['doctrine_mongodb']->getManager();

        $schemaManager = $this->getSchemaManager($dm);

        $this->createSchema($schemaManager);
        $this->dropSchema($schemaManager);
    }

    public function testValidator()
    {
        $app = $this->createApplication();

        /** @var DocumentManager $dm */
        $dm = $app['doctrine_mongodb']->getManager();

        /** @var Validator $validator */
        $validator = $app['validator'];

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

    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;

        $app->register(new ValidatorServiceProvider());
        $app->register(new DoctrineMongoDbProvider(), array(
            'mongodb.options' => array(
                'server' => 'mongodb://localhost:27017',
//                'options' => array(
//                    'username' => 'root',
//                    'password' => 'root',
//                    'db' => 'admin'
//                )
            )
        ));
        $app->register(new DoctrineMongoDbOdmProvider(), array(
            "mongodbodm.proxies_dir" => $this->getCacheDir() . '/doctrine/proxies',
            "mongodbodm.hydrator_dir" => $this->getCacheDir() . '/doctrine/hydrator',
            'mongodbodm.dm.options' => array(
                'mappings' => array(
                    array(
                        'type' => 'annotation',
                        'namespace' => 'Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Document',
                        'path' => __DIR__.'/../../Document',
                        'use_simple_annotation_reader' => false,
                    )
                )
            )
        ));
        $app->register(new DoctrineMongodbOdmManagerRegistryProvider());

        return $app;
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
        $cacheDir =  __DIR__ . '/../../cache';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        return $cacheDir;
    }
}
