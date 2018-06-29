<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\BookCopy;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Rating
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rating")
 */
class Rating
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *     min=1,
     *     max=10,
     * )
     */
    private $rating;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="rateAuthored")
     */
    private $author;

    /**
     * @var BookCopy
     * @ORM\ManyToOne(targetEntity="BookCopy", inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bookCopy;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $date;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return \App\Entity\BookCopy
     */
    public function getBookCopy(): BookCopy
    {
        return $this->bookCopy;
    }

    /**
     * @param \App\Entity\BookCopy $bookCopy
     */
    public function setBookCopy(BookCopy $bookCopy): void
    {
        $this->bookCopy = $bookCopy;
    }
}