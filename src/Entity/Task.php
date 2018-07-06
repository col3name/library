<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Task
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="task")
 */
class Task
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="tasks", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="task_genre")
     */
    private $tags;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Author", inversedBy="tasks", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="task_author")
     */
    private $authors;

    /**
     * Task constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    /**
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * @return ArrayCollection
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param Author $author
     */
    public function addAuthor(Author $author)
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }
    }

    /**
     * @param Author $author
     */
    public function removeAuthor(Author $author)
    {
        $this->authors->removeElement($author);
    }
}