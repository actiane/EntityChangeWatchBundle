<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Entity
 *
 * @ORM\Entity
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;
    /**
     * @ORM\Column(type="string")
     */
    private ?string $title = null;
    /**
     * @var Collection|SubEntity[]
     *
     * @ORM\OneToMany(targetEntity="SubEntity", mappedBy="entity")
     */
    private Collection $subEntities;
    /**
     * @ORM\OneToOne(targetEntity="SubEntityOneToOne")
     */
    private ?SubEntityOneToOne $subEntitiesOneToOne = null;

    /**
     */
    public function __construct()
    {
        $this->subEntities = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Entity
     */
    public function setId(int $id): Entity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
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
    public function addSubEntity(SubEntity $subEntity): Entity
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
    public function removeSubEntity(SubEntity $subEntity): Entity
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
     * @return SubEntityOneToOne|null
     */
    public function getSubEntitiesOneToOne(): ?SubEntityOneToOne
    {
        return $this->subEntitiesOneToOne;
    }

    /**
     * @param SubEntityOneToOne $subEntitiesOneToOne
     *
     * @return Entity
     */
    public function setSubEntitiesOneToOne(SubEntityOneToOne $subEntitiesOneToOne): Entity
    {
        $this->subEntitiesOneToOne = $subEntitiesOneToOne;

        return $this;
    }
}
