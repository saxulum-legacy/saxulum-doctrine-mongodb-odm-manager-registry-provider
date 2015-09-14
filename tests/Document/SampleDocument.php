<?php

namespace Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Saxulum\DoctrineMongodbOdmManagerRegistry\Validator\Constraints\Unique;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ODM\Document()
 */
class SampleDocument
{
    /**
     * @var string
     * @ODM\Id(strategy="auto")
     */
    protected $id;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new Unique(array(
            'fields'  => 'name',
            'message' => 'This name already exists.',
        )));
    }
}
