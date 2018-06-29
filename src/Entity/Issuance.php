<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\BookCopy;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="issuance")
 */
class Issuance
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userIssuance")
     */
    private $reader;

    /**
     * @var BookCopy
     * @ORM\ManyToOne(targetEntity="App\Entity\BookCopy", inversedBy="issuances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bookCopy;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $issueDate;

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
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     */
    private $releaseDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     */
    private $deadlineDate;

    public function __construct()
    {
        $this->issueDate = new \DateTime();
        $this->releaseDate = null;
        $this->deadlineDate = new \DateTime('now + 14day');
    }

    /**
     * @return mixed
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @param mixed $reader
     */
    public function setReader($reader): void
    {
        $this->reader = $reader;
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
    public function getIssueDate(): \DateTime
    {
        return $this->issueDate;
    }

    /**
     * @param \DateTime $issueDate
     */
    public function setIssueDate(\DateTime $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

    /**
     * @return \DateTime
     */
    public function getReleaseDate(): \DateTime
    {
        return $this->releaseDate;
    }

    /**
     * @param \DateTime $releaseDate
     */
    public function setReleaseDate(\DateTime $releaseDate): void
    {
        $this->releaseDate = $releaseDate;
    }

    /**
     * @return \DateTime
     */
    public function getDeadlineDate(): \DateTime
    {
        return $this->deadlineDate;
    }

    /**
     * @param \DateTime $deadlineDate
     */
    public function setDeadlineDate(\DateTime $deadlineDate): void
    {
        $this->deadlineDate = $deadlineDate;
    }
}