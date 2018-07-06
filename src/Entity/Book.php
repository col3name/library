<?php

namespace App\Entity;

use App\Entity\Genre;
use App\Entity\Author;
use App\Entity\BookCopy;
//use App\Entity\PublishingHouse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Book
 * @package App\Entity
 * @ORM\Entity(repositoryClass="BookRepository")
 * @ORM\Table(name="book")
 */
class Book
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min=20,
     *     minMessage="book description should have more than 20 symbol")
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $isbn;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $pageNumber;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *     min=-3000,
     *     max=4000
     * )
     */
    private $publicationYear;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="books", cascade={"persist"})
     * @ORM\JoinTable(name="book_genre")
     */
    private $genresBook;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Author", inversedBy="books", cascade={"persist"})
     * @ORM\JoinTable(name="author_book")
     */
    private $authorsBook;

    /**
     * @ORM\OneToMany(targetEntity="BookCopy", mappedBy="book", cascade={"persist", "remove"}))
     */
    public $bookCopy;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinTable(name="book_tag")
     */
    private $tags;

    /**
     * Book constructor.
     * @param string $name
     * @param string $description
     * @param string $isbn
     * @param string $pageNumber
     * @param string $publicationYear
     */
    public function __construct($name = '', $description = '', $isbn = '', $pageNumber = '', $publicationYear = '')
    {
        $this->name = $name;
        $this->description = $description;
        $this->isbn = $isbn;
        $this->pageNumber = $pageNumber;
        $this->publicationYear = $publicationYear;
        $this->authorsBook = new ArrayCollection();
        $this->genresBook = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
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
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     */
    public function setIsbn(string $isbn)
    {
        $this->isbn = $isbn;
    }

    /**
     * @return int
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * @param int $pageNumber
     */
    public function setPageNumber(int $pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    /**
     * @return int
     */
    public function getPublicationYear()
    {
        return $this->publicationYear;
    }

    /**
     * @param int $publicationYear
     */
    public function setPublicationYear(int $publicationYear)
    {
        $this->publicationYear = $publicationYear;
    }

    /**
     * @return mixed
     */
    public function getGenresBook()
    {
        return $this->genresBook;
    }

    /**
     * @param mixed $genresBook
     */
    public function setGenresBook($genresBook)
    {
        $this->genresBook = $genresBook;
    }

    /**
     * @param \App\Entity\Genre $genre
     */
    public function addGenresBook(Genre $genre)
    {
        if (!$this->tags->contains($genre)) {
            $this->genresBook->add($genre);
        }
    }

    /**
     * @param \App\Entity\Genre $genre
     */
    public function removeGenresBook(Genre $genre)
    {
        $this->genresBook->removeElement($genre);
    }

    /**
     * @return mixed
     */
    public function getAuthorsBook()
    {
        return $this->authorsBook;
    }

    /**
     * @param \App\Entity\Author $author
     */
    public function addAuthorsBook(Author $author)
    {
        if (!$this->isAuthorAlreadyAdded($author)) {
            $this->authorsBook->add($author);
        }
    }

    /**
     * @param \App\Entity\Author $author
     */
    public function removeAuthorsBook(Author $author)
    {
        $this->authorsBook->removeElement($author);
    }

    /**
     * @param mixed $authorsBook
     */
    public function setAuthorsBook($authorsBook)
    {
        $this->authorsBook = $authorsBook;
    }

    /**
     * @param \App\Entity\Author $author
     * @return bool
     */
    public function isAuthorAlreadyAdded(Author $author)
    {
        return $this->authorsBook->contains($author);
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
}