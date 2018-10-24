<?php

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Entity
 *
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity
 * @ORM\Entity
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var SubEntity[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="SubEntity", mappedBy="entity")
     */
    private $subEntities;

    /**
     * @var SubEntityOneToOne
     * @ORM\OneToOne(targetEntity="SubEntityOneToOne")
     */
    private $subEntitiesOneToOne;

    /**
     * Entity constructor.
     */
    public function __construct()
    {
        $this->subEntities = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Entity
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Entity
     */
    public function setTitle(string $title): Entity
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return SubEntity[]|ArrayCollection
     */
    public function getSubEntities()
    {
        return $this->subEntities;
    }

    /**
     * @param SubEntity $subEntity
     *
     * @return Entity
     */
    public function addSubEntity($subEntity)
    {
        $this->subEntities->add($subEntity);
        $subEntity->setEntity($this);

        return $this;
    }

    /**
     * @param SubEntity $subEntity
     *
     * @return Entity
     */
    public function removeSubEntity($subEntity)
    {
        $this->subEntities->removeElement($subEntity);

        return $this;
    }

    /**
     * @param SubEntity[]|ArrayCollection $subEntities
     *
     * @return Entity
     */
    public function setSubEntities($subEntities)
    {
        $this->subEntities = $subEntities;

        return $this;
    }

    /**
     * @return SubEntityOneToOne
     */
    public function getSubEntitiesOneToOne()
    {
        return $this->subEntitiesOneToOne;
    }

    /**
     * @param SubEntityOneToOne $subEntitiesOneToOne
     *
     * @return Entity
     */
    public function setSubEntitiesOneToOne($subEntitiesOneToOne)
    {
        $this->subEntitiesOneToOne = $subEntitiesOneToOne;

        return $this;
    }
}
