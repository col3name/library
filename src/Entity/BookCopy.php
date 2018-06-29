<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\Issuance;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BookCopy
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="bookCopy")
 */
class BookCopy
{
    public const IMAGE_UPLOAD_DIRECTORY = 'upload/book/image/';
    public const FILE_UPLOAD_DIRECTORY = 'upload/book/file/';

    public const NUM_ITEMS = 20;
    public const MAX_COUNT = 4;

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Book
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="bookCopy", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *     min=0,
     *     max=4
     * )
     */
    private $count;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Please, upload the book upload")
     * @Assert\Image(
     *     minWidth = 200,
     *     maxWidth = 1920,
     *     minHeight = 300,
     *     maxHeight = 1920
     * )
     */
    private $imagePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(
     *     maxSize = "20M",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     */
    private $filePath;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $uploadDate;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="User", mappedBy="favoritesBookCopy")
     */
    private $userFavoritesBook;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="User", mappedBy="readedBookCopy")
     */
    private $userReadedBookCopy;

    /**
     * @ORM\OneToMany(targetEntity="Rating", mappedBy="bookCopy")
     */
    private $rates;

    /**
     * @ORM\OneToMany(targetEntity="Issuance", mappedBy="bookCopy")
     */
    private $issuances;

    /**
     * @var Comment[]|ArrayCollection
     * @ORM\OrderBy({"date": "DESC"})
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="bookCopy")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="favoritesComment")
     */
    private $userFavorites;

    /**
     * BookCopy constructor.
     */
    public function __construct()
    {
        $this->uploadDate = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->userFavoritesBook = new ArrayCollection();
        $this->userReadedBookCopy = new ArrayCollection();
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
     * @return \App\Entity\Book
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * @return mixed
     */
    public function getUserFavorites()
    {
        return $this->userFavorites;
    }

    /**
     * @param mixed $userFavorites
     */
    public function setUserFavorites($userFavorites)
    {
        $this->userFavorites = $userFavorites;
    }

    /**
     * @param \App\Entity\Book $book
     */
    public function setBook(Book $book)
    {
        $this->book = $book;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @param mixed $imagePath
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param mixed $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return mixed
     */
    public function getUserFavoritesBook()
    {
        return $this->userFavoritesBook;
    }

    /**
     * @param mixed $userFavoritesBook
     */
    public function setUserFavoritesBook($userFavoritesBook): void
    {
        $this->userFavoritesBook = $userFavoritesBook;
    }

    /**
     * @return ArrayCollection
     */
    public function getUserReadedBookCopy(): ArrayCollection
    {
        return $this->userReadedBookCopy;
    }

    /**
     * @param ArrayCollection $userReadedBookCopy
     */
    public function setUserReadedBookCopy(ArrayCollection $userReadedBookCopy): void
    {
        $this->userReadedBookCopy = $userReadedBookCopy;
    }


    /**
     * @return \DateTime
     */
    public function getUploadDate(): \DateTime
    {
        return $this->uploadDate;
    }

    /**
     * @param \DateTime $uploadDate
     */
    public function setUploadDate(\DateTime $uploadDate)
    {
        $this->uploadDate = $uploadDate;
    }

    /**
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function addComment(?Comment $comment): void
    {
        $comment->setBookCopy($this);
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
        }
    }

    /**
     * @param \App\Entity\Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $comment->setBookCopy(null);
        $this->comments->removeElement($comment);
    }

    /**
     * @return mixed
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * @param mixed $rates
     */
    public function setRates($rates): void
    {
        $this->rates = $rates;
    }

    /**
     * @return mixed
     */
    public function getIssuances()
    {
        return $this->issuances;
    }

    /**
     * @param mixed $issuances
     */
    public function setIssuances($issuances): void
    {
        $this->issuances = $issuances;
    }
//
//    /**
//     * @return Author[]|ArrayCollection
//     */
//    public function getAuthors()
//    {
//        return $this->authors;
//    }
//
//    /**
//     * @param Author|null $author
//     */
//    public function addAuthor(?Author $author)
//    {
//        $author->setBooks($this);
//        if (!$this->authors->contains($author)) {
//            $this->authors->add($author);
//        }
//    }
//
//    /**
//     * @param \App\Entity\Author $bookAuthor
//     */
//    public function removeAuthor(Author $bookAuthor)
//    {
//        $bookAuthor->setBooks(null);
//        $this->authors->removeElement($bookAuthor);
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getUserFavorites()
//    {
//        return $this->userFavorites;
//    }
//
//    /**
//     * @param $userFavorite
//     * @return Comment
//     */
//    public function addUserFavorites($userFavorite): self
//    {
//        if (!$this->userFavorites->contains($userFavorite)) {
//            $this->userFavorites[] = $userFavorite;
//            $userFavorite->setCategory($this);
//        }
//
//        return $this;
//    }
}