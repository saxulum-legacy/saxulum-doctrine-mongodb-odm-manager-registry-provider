<?php

namespace Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Doctrine;

use Saxulum\DoctrineMongodbOdmManagerRegistry\Provider\DoctrineMongodbOdmManagerRegistryProvider;

class ManagerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    protected function createMockDefaultAppAndDeps()
    {
        $app = new \Pimple;

        $connection = $this
            ->getMockBuilder('Doctrine\MongoDB\Connection')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $app['mongodbs'] = new \Pimple(array(
            'default' => $connection,
        ));

        $app['mongodbs.default'] = 'default';

        $configuration = $this->getMock('Doctrine\ODM\MongoDB\Configuration');

        $configuration
            ->expects($this->any())
            ->method('getDocumentNamespace')
            ->will($this->returnValue('Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Doctrine\ManagerRegistryTest'))
        ;

        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $metadataFactory = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory');

        $documentManager = $this
            ->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $documentManager
            ->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration))
        ;

        $documentManager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository))
        ;

        $documentManager
            ->expects($this->any())
            ->method('getMetadataFactory')
            ->will($this->returnValue($metadataFactory))
        ;

        $app['mongodbodm.dms'] = new \Pimple(array(
            'default' => $documentManager,
        ));

        $app['mongodbodm.dms.default'] = 'default';

        return $app;
    }

    public function testRegisterDefaultImplementations()
    {
        $app = $this->createMockDefaultAppAndDeps();

        $doctrineMongoDbOdmManagerRegistryProvider = new DoctrineMongodbOdmManagerRegistryProvider();
        $doctrineMongoDbOdmManagerRegistryProvider->register($app);

        $this->assertEquals('default', $app['doctrine_mongodb']->getDefaultConnectionName());
        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $app['doctrine_mongodb']->getConnection());
        $this->assertCount(1, $app['doctrine_mongodb']->getConnections());
        $this->assertCount(1, $app['doctrine_mongodb']->getConnectionNames());
        $this->assertEquals('default', $app['doctrine_mongodb']->getDefaultManagerName());
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $app['doctrine_mongodb']->getManager());
        $this->assertCount(1, $app['doctrine_mongodb']->getManagers());
        $this->assertCount(1, $app['doctrine_mongodb']->getManagerNames());
        $this->assertEquals($app['doctrine_mongodb']->getAliasNamespace('Test'), 'Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Doctrine\ManagerRegistryTest');
        $this->assertInstanceOf('Doctrine\Common\Persistence\ObjectRepository', $app['doctrine_mongodb']->getRepository('Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Doctrine\ManagerRegistryTest'));
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $app['doctrine_mongodb']->getManagerForClass('Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Doctrine\ManagerRegistryTest'));
    }
}