saxulum-doctrine-mongodb-odm-manager-registry-provider
======================================================

[![Build Status](https://api.travis-ci.org/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider.png?branch=master)](https://travis-ci.org/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider)
[![Total Downloads](https://poser.pugx.org/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider/downloads.png)](https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider)
[![Latest Stable Version](https://poser.pugx.org/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider/v/stable.png)](https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider/badges/quality-score.png)](https://scrutinizer-ci.com/g/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider)

Features
--------

 * Leverages the core [Doctrine MongoDB Service Provider][1] for either Silex or Cilex and the [Doctrine MongoDB ODM Service Provider][2]
 * The Registry manager registry can the used with the [Doctrine Bridge][4] from symfony, to use entity type in the [Symfony Form Component][5] 

Requirements
------------

 * PHP 5.3+
 * Doctrine MongoDB ODM ~1.0
 
Currently requires both **mongodbs** and **mongodbodm.dms** services in order to work.
These can be provided by a [Doctrine MongoDB Service Provider][1] and the [Doctrine MongoDB ODM Service Provider][2] service providers.
If you can or want to fake it, go for it. :)

Installation
------------
 
Through [Composer](http://getcomposer.org) as [saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider][6].

```{.sh}
composer require "saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider": "dev-master@dev"
```

```{.php}
<?php

use Saxulum\DoctrineMongodbOdmManagerRegistry\Silex\Provider\DoctrineMongodbOdmManagerRegistryProvider;

$app->register(new DoctrineMongodbOdmManagerRegistryProvider());
```

### Form Entity Type

If you like to have `Entity` Type Support within [Symfony Form Component][5], install the [Doctrine Bridge][4] and register the form provider first.

```{.json}
{
    "require": {
        "symfony/doctrine-bridge": "~2.2",
        "symfony/form": "~2.2"
    }
}
```

```{.php}
<?php

use Saxulum\DoctrineMongodbOdmManagerRegistry\Silex\Provider\DoctrineMongodbOdmManagerRegistryProvider;
use Silex\Provider\FormServiceProvider;

$app->register(new FormServiceProvider());
$app->register(new DoctrineMongodbOdmManagerRegistryProvider());
```

### Validator

If you like to have `UniqueEntity` Constraint Support within [Symfony Validator Component][9], install the [Doctrine Bridge][4] and register the validator provider first.

```{.json}
{
    "require": {
        "symfony/doctrine-bridge": "~2.2",
        "symfony/validator": "~2.2"
    }
}
```

```{.php}
<?php

use Saxulum\DoctrineMongodbOdmManagerRegistry\Silex\Provider\DoctrineMongodbOdmManagerRegistryProvider;
use Silex\Provider\ValidatorServiceProvider;

$app->register(new ValidatorServiceProvider());
$app->register(new DoctrineMongodbOdmManagerRegistryProvider());
```

```{.php}
<?php

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sample")
 */
class Sample
{
    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity(array(
            'fields'  => 'name',
            'message' => 'This name already exists.',
        )));
    }
}
```

### Symfony Console

If you like to use [Symfony Console][7] commands, install [Symfony Console][7] and the [Saxulum Console Provider][8] and register the console provider.

```{.json}
{
    "require": {
        "saxulum/saxulum-doctrine-mongodb-odm-commands": "dev-master@dev",
        "saxulum/saxulum-console": "~2.0",
    }
}
```

```{.php}
<?php

use Saxulum\DoctrineMongodbOdmManagerRegistry\Silex\Provider\DoctrineMongodbOdmManagerRegistryProvider;
use Saxulum\Console\Silex\Provider\ConsoleProvider;

$app->register(new ConsoleProvider());
$app->register(new DoctrineMongodbOdmManagerRegistryProvider());
```

Usage
-----

```{.php}
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
[4]: https://github.com/symfony/DoctrineBridge
[5]: https://github.com/symfony/Form
[6]: https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-odm-manager-registry-provider
[7]: https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-odm-commands
[8]: https://packagist.org/packages/saxulum/saxulum-console
[9]: https://github.com/symfony/Validator
