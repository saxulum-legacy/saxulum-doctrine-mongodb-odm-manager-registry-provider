<?php

namespace Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Doctrine;

use Pimple\Container;
use Saxulum\DoctrineMongodbOdmManagerRegistry\Provider\DoctrineMongodbOdmManagerRegistryProvider;

class ManagerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Container
     */
    protected function createMockDefaultAppAndDeps()
    {
        $container = new Container();

        $connection = $this
            ->getMockBuilder('Doctrine\MongoDB\Connection')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $container['mongodbs'] = new Container(array(
            'default' => $connection,
        ));

        $container['mongodbs.default'] = 'default';

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

        $container['mongodbodm.dms'] = new Container(array(
            'default' => $documentManager,
        ));

        $container['mongodbodm.dms.default'] = 'default';

        return $container;
    }

    public function testRegisterDefaultImplementations()
    {
        $container = $this->createMockDefaultAppAndDeps();

        $doctrineMongoDbOdmManagerRegistryProvider = new DoctrineMongodbOdmManagerRegistryProvider();
        $doctrineMongoDbOdmManagerRegistryProvider->register($container);

        $this->assertEquals('default', $container['doctrine_mongodb']->getDefaultConnectionName());
        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $container['doctrine_mongodb']->getConnection());
        $this->assertCount(1, $container['doctrine_mongodb']->getConnections());
        $this->assertCount(1, $container['doctrine_mongodb']->getConnectionNames());
        $this->assertEquals('default', $container['doctrine_mongodb']->getDefaultManagerName());
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $container['doctrine_mongodb']->getManager());
        $this->assertCount(1, $container['doctrine_mongodb']->getManagers());
        $this->assertCount(1, $container['doctrine_mongodb']->getManagerNames());
        $this->assertEquals($container['doctrine_mongodb']->getAliasNamespace('Test'), 'Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Doctrine\ManagerRegistryTest');
        $this->assertInstanceOf('Doctrine\Common\Persistence\ObjectRepository', $container['doctrine_mongodb']->getRepository('Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Doctrine\ManagerRegistryTest'));
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $container['doctrine_mongodb']->getManagerForClass('Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Doctrine\ManagerRegistryTest'));
    }
}
