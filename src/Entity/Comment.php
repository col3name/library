<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\BookCopy;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment")
 */
class Comment
{
    public const COMMENT_LIMIT = 5;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(
     *     min=5,
     *     max=10000,
     * )
     */
    private $text;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="favoritesComment")
     */
    private $userFavorites;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="commentsAuthored")
     */
    private $author;

    /**
     * @var BookCopy
     *
     * @ORM\ManyToOne(targetEntity="BookCopy", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bookCopy;

    /**
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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
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
    public function getUserFavorites()
    {
        return $this->userFavorites;
    }

    /**
     * @param $userFavorite
     * @return Comment
     */
    public function addUserFavorites($userFavorite): self
    {
        if (!$this->userFavorites->contains($userFavorite)) {
            $this->userFavorites[] = $userFavorite;
            $userFavorite->setCategory($this);
        }

        return $this;
    }


}