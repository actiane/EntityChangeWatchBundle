<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class SubEntity
 *
 * @ORM\Entity
 */
class SubEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(type="string")
     */
    private ?string $field = null;
    /**
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="subEntities")
     */
    private ?Entity $entity = null;

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
     * @return SubEntity
     */
    public function setId(int $id): SubEntity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return SubEntity
     */
    public function setField(string $field): SubEntity
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return Entity|null
     */
    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     *
     * @return SubEntity
     */
    public function setEntity(Entity $entity): SubEntity
    {
        $this->entity = $entity;

        return $this;
    }
}
