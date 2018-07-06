<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */
class Tag
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Task", mappedBy="tags", cascade={"persist", "remove"})
     */
    private $tasks;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Book", mappedBy="tags", cascade={"persist", "remove"})
     */
    private $books;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @param mixed $tasks
     */
    public function setTasks($tasks): void
    {
        $this->tasks = $tasks;
    }

    /**
     * @param Task $task
     */
    public function addTask(Task $task)
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
        }
    }

    /**
     * @param Task $task
     */
    public function removeTask(Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * @return ArrayCollection
     */
    public function getBooks(): ArrayCollection
    {
        return $this->books;
    }

    /**
     * @param ArrayCollection $books
     */
    public function setBooks(ArrayCollection $books): void
    {
        $this->books = $books;
    }

    /**
     * @param Book $book
     */
    public function addBook(Book $book)
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
        }
    }

    /**
     * @param Task $book
     */
    public function removeBook(Task $book)
    {
        $this->books->removeElement($book);
    }

}