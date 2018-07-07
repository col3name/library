<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Rating
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rating")
 */
class Rating extends BaseEntity
{
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
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
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