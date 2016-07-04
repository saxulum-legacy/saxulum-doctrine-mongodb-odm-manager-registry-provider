<?php

namespace Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Saxulum\DoctrineMongodbOdmManagerRegistry\Validator\Constraints\Unique;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ODM\Document()
 */
class Category
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
     * @ODM\ReferenceMany(
     *     targetDocument="Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Document\Document",
     *     mappedBy="categories"
     * )
     */
    public $documents;

    public function __construct($name)
    {
        $this->name = $name;
        $this->documents = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Document $document
     * @param bool $stopPropagation
     * @return $this
     */
    public function addDocument(Document $document, $stopPropagation = false)
    {
        $this->documents->add($document);
        if(!$stopPropagation) {
            $document->addCategory($this, true);
        }
        return $this;
    }

    /**
     * @param Document $document
     * @param bool $stopPropagation
     * @return $this
     */
    public function removeDocument(Document $document, $stopPropagation = false)
    {
        $this->documents->removeElement($document);
        if(!$stopPropagation) {
            $document->removeCategory($this, true);
        }
        return $this;
    }

    /**
     * @param Document[] $documents
     * @return $this
     */
    public function setDocuments($documents)
    {
        foreach($this->documents as $document) {
            $this->removeDocument($document);
        }
        foreach($documents as $document) {
            $this->addDocument($document);
        }
        return $this;
    }

    /**
     * @return Document[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
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
