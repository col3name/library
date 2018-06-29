<?php

namespace App\Entity;

use App\Entity\Book;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PublishingHouse
 * @package App\Entity
 * Class PublishingHouse
 * @ORM\Entity()
 * @ORM\Table(name="publishing_house")
 */
class PublishingHouse
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="publishingHousesBook")
     * @ORM\JoinTable(name="publishing_house_of_book")
     */
    private $books;
}