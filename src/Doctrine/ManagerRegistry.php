<?php

namespace Saxulum\DoctrineMongodbOdmManagerRegistry\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry as ManagerRegistryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\MongoDBException;
use Pimple\Container;

class ManagerRegistry implements ManagerRegistryInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Connection[]
     */
    protected $connections;

    /**
     * @var string
     */
    protected $defaultConnectionName;

    /**
     * @var ObjectManager[]
     */
    protected $managers;

    /**
     * @var string
     */
    protected $defaultManagerName;

    /**
     * @var string
     */
    protected $proxyInterfaceName;

    /**
     * @param Container $container
     * @param string  $proxyInterfaceName
     */
    public function __construct(Container $container, $proxyInterfaceName = 'Doctrine\ODM\MongoDB\Proxy\Proxy')
    {
        $this->container = $container;
        $this->proxyInterfaceName = $proxyInterfaceName;
    }

    /**
     * @return string
     */
    public function getDefaultConnectionName()
    {
        $this->loadConnections();

        return $this->defaultConnectionName;
    }

    /**
     * @param  string|null               $name
     * @return Connection
     * @throws \InvalidArgumentException
     */
    public function getConnection($name = null)
    {
        $this->loadConnections();

        if ($name === null) {
            $name = $this->getDefaultConnectionName();
        }

        if (!isset($this->connections[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Connection named "%s" does not exist.', $name));
        }

        return $this->connections[$name];
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        $this->loadConnections();

        if ($this->connections instanceof Container) {
            $connections = array();
            foreach ($this->getConnectionNames() as $name) {
                $connections[$name] = $this->connections[$name];
            }
            $this->connections = $connections;
        }

        return $this->connections;
    }

    /**
     * @return array
     */
    public function getConnectionNames()
    {
        $this->loadConnections();

        if ($this->connections instanceof Container) {
            return $this->connections->keys();
        } else {
            return array_keys($this->connections);
        }
    }

    protected function loadConnections()
    {
        if (is_null($this->connections)) {
            $this->connections = $this->container['mongodbs'];
            $this->defaultConnectionName = $this->container['mongodbs.default'];
        }
    }

    /**
     * @return string
     */
    public function getDefaultManagerName()
    {
        $this->loadManagers();

        return $this->defaultManagerName;
    }

    /**
     * @param  null                      $name
     * @return ObjectManager
     * @throws \InvalidArgumentException
     */
    public function getManager($name = null)
    {
        $this->loadManagers();

        if ($name === null) {
            $name = $this->getDefaultManagerName();
        }

        if (!isset($this->managers[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Manager named "%s" does not exist.', $name));
        }

        return $this->managers[$name];
    }

    /**
     * @return array
     */
    public function getManagers()
    {
        $this->loadManagers();

        if ($this->managers instanceof Container) {
            $managers = array();
            foreach ($this->getManagerNames() as $name) {
                $managers[$name] = $this->managers[$name];
            }
            $this->managers = $managers;
        }

        return $this->managers;
    }

    /**
     * @return array
     */
    public function getManagerNames()
    {
        $this->loadManagers();

        if ($this->managers instanceof Container) {
            return $this->managers->keys();
        } else {
            return array_keys($this->managers);
        }
    }

    /**
     * @param  null                      $name
     * @return void
     * @throws \InvalidArgumentException
     */
    public function resetManager($name = null)
    {
        $this->loadManagers();

        if (null === $name) {
            $name = $this->getDefaultManagerName();
        }

        if (!isset($this->managers[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Manager named "%s" does not exist.', $name));
        }

        $this->managers[$name] = null;
    }

    protected function loadManagers()
    {
        if (is_null($this->managers)) {
            $this->managers = $this->container['mongodbodm.dms'];
            $this->defaultManagerName = $this->container['mongodbodm.dms.default'];
        }
    }

    /**
     * @param  string       $alias
     * @return string
     * @throws MongoDBException
     */
    public function getAliasNamespace($alias)
    {
        foreach ($this->getManagerNames() as $name) {
            try {
                return $this->getManager($name)->getConfiguration()->getDocumentNamespace($alias);
            } catch (MongoDBException $e) {
            }
        }
        throw MongoDBException::unknownDocumentNamespace($alias);
    }

    /**
     * @param  string           $persistentObject
     * @param  null             $persistentManagerName
     * @return ObjectRepository
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        return $this->getManager($persistentManagerName)->getRepository($persistentObject);
    }

    /**
     * @param  string             $class
     * @return ObjectManager|null
     */
    public function getManagerForClass($class)
    {
        $proxyClass = new \ReflectionClass($class);
        if ($proxyClass->implementsInterface($this->proxyInterfaceName)) {
            $class = $proxyClass->getParentClass()->getName();
        }

        foreach ($this->getManagerNames() as $managerName) {
            if (!$this->getManager($managerName)->getMetadataFactory()->isTransient($class)) {
                return $this->getManager($managerName);
            }
        }
    }
}
