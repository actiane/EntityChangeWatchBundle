<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class NotWatchedEntity
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity
 * @ORM\Entity
 */
class NotWatchedEntity
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return NotWatchedEntity
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
     * @return NotWatchedEntity
     */
    public function setTitle(string $title): NotWatchedEntity
    {
        $this->title = $title;

        return $this;
    }
}
