<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class NotWatchedEntity
 *
 * @ORM\Entity
 */
class NotWatchedEntity
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
    private ?string $title = null;

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
     * @return NotWatchedEntity
     */
    public function setId(int $id): NotWatchedEntity
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
     * @return NotWatchedEntity
     */
    public function setTitle(string $title): NotWatchedEntity
    {
        $this->title = $title;

        return $this;
    }
}
