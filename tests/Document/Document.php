<?php

namespace Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Saxulum\DoctrineMongodbOdmManagerRegistry\Validator\Constraints\Unique;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ODM\Document()
 */
class Document
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
     * @var ArrayCollection
     * @ODM\ReferenceMany(
     *     targetDocument="Saxulum\Tests\DoctrineMongodbOdmManagerRegistry\Document\Category",
     *     inversedBy="documents"
     * )
     */
    public $categories;

    /**
     * @param string $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categories = new ArrayCollection();
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
     * @param Category $category
     * @param bool $stopPropagation
     * @return $this
     */
    public function addCategory(Category $category, $stopPropagation = false)
    {
        $this->categories->add($category);
        if(!$stopPropagation) {
            $category->addDocument($this, true);
        }
        return $this;
    }

    /**
     * @param Category $category
     * @param bool $stopPropagation
     * @return $this
     */
    public function removeCategory(Category $category, $stopPropagation = false)
    {
        $this->categories->removeElement($category);
        if(!$stopPropagation) {
            $category->removeDocument($this, true);
        }
        return $this;
    }

    /**
     * @param Category[] $categories
     * @return $this
     */
    public function setCategories($categories)
    {
        foreach($this->categories as $category) {
            $this->removeCategory($category);
        }
        foreach($categories as $category) {
            $this->addCategory($category);
        }
        return $this;
    }

    /**
     * @return Category[]
     */
    public function getCategories()
    {
        return $this->categories;
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
