<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class SubEntity
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity
 * @ORM\Entity
 */
class SubEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $field;

    /**
     * @var Entity
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="subEntities")
     */
    private $entity;

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
     * @return SubEntity
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getField(): string
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
     * @return Entity
     */
    public function getEntity(): Entity
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
