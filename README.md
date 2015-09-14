saxulum-doctrine-mongodb-odm-manager-registry-provider
======================================================

[![Build Status](https://api.travis-ci.org/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider.png?branch=master)](https://travis-ci.org/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider)
[![Total Downloads](https://poser.pugx.org/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider/downloads.png)](https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider)
[![Latest Stable Version](https://poser.pugx.org/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider/v/stable.png)](https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider)

Features
--------

 * Leverages the core [Doctrine MongoDB Service Provider][1] for either Silex or Cilex and the [Doctrine MongoDB ODM Service Provider][2]
 * The Registry manager registry can the used, to use entity type in the [Symfony Form Component][3]

Requirements
------------

 * PHP 5.3+
 * Doctrine MongoDB ODM ~1.0
 
Currently requires both **mongodbs** and **mongodbodm.dms** services in order to work.
These can be provided by a [Doctrine MongoDB Service Provider][1] and the [Doctrine MongoDB ODM Service Provider][2] service providers.
If you can or want to fake it, go for it. :)

Installation
------------
 
Through [Composer](http://getcomposer.org) as [saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider][4].

```php
$app->register(new Saxulum\DoctrineMongodbOdmManagerRegistry\Provider\Silex\DoctrineMongodbOdmManagerRegistryProvider());
```

Usage
-----

```php
<?php

// get the default connection name
$app['doctrine_mongodb']->getDefaultConnectionName();

// get the default connection 
$app['doctrine_mongodb']->getConnection();

// get a connection by name
$app['doctrine_mongodb']->getConnection('name');

// all connections as array access (pimple)
$app['doctrine_mongodb']->getConnections();

// all connection names as array
$app['doctrine_mongodb']->getConnectionNames();

// get the default manager name
$app['doctrine_mongodb']->getDefaultManagerName();

// get the default manager
$app['doctrine_mongodb']->getManager();

// get a manager by name
$app['doctrine_mongodb']->getManager('name');

// all manager as array access (pimple)
$app['doctrine_mongodb']->getManagers();

// all manager names as array
$app['doctrine_mongodb']->getManagerNames();
...
```

[1]: https://github.com/saxulum/saxulum-doctrine-mongodb-provider
[2]: https://github.com/saxulum/saxulum-doctrine-mongodb-odm-provider
[3]: https://github.com/symfony/Form
[4]: https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider
