<?php

namespace App\Entity;

use App\Entity\Comment;
use App\Entity\Rating;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface, \Serializable
{
    const IMAGE_UPLOAD_DIRECTORY = 'upload/book/image/';
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @Assert\Length(max=64)
     * @ORM\Column(type="string", length=64)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

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
    private $avatar;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Comment", inversedBy="userFavorites")
     * @ORM\JoinTable(name="user_favorites_comment")
     */
    private $favoritesComment;

    /**
     * @ORM\OneToMany(targetEntity="Rating", mappedBy="author")
     */
    private $rateAuthored;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="author")
     */
    private $commentsAuthored;

    /**
     * @ORM\OneToMany(targetEntity="Issuance", mappedBy="reader")
     */
    private $userIssuance;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="BookCopy", inversedBy="userFavoritesBook")
     * @ORM\JoinTable(name="favorite_book_copy")
     */
    private $favoritesBookCopy;

    /**
     * @return mixed
     */
    public function getFavoritesComment()
    {
        return $this->favoritesComment;
    }

    /**
     * @param mixed $favoritesComment
     */
    public function setFavoritesComment($favoritesComment): void
    {
        $this->favoritesComment = $favoritesComment;
    }

    public function addFavoritesBookCopy(BookCopy $bookCopy) {
        if ($this->bookCopyLiked($bookCopy)) {
            return;
        }

        $this->favoritesBookCopy[] = $bookCopy;
    }

    public function removeFavoritesBookCopy(BookCopy $bookCopy) {
        $this->favoritesBookCopy->removeElement($bookCopy);
    }

    public function bookCopyLiked(BookCopy $bookCopy)
    {
        return $this->favoritesBookCopy->contains($bookCopy);
    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->isActive = true;
        $this->avatar = "/image/camera_200.png";
        $this->favoritesBookCopy = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getRateAuthored()
    {
        return $this->rateAuthored;
    }

    /**
     * @param mixed $rateAuthored
     */
    public function setRateAuthored($rateAuthored): void
    {
        $this->rateAuthored = $rateAuthored;
    }

    /**
     * @return mixed
     */
    public function getCommentsAuthored()
    {
        return $this->commentsAuthored;
    }

    /**
     * @param mixed $commentsAuthored
     */
    public function setCommentsAuthored($commentsAuthored): void
    {
        $this->commentsAuthored = $commentsAuthored;
    }

    /**
     * @return mixed
     */
    public function getUserIssuance()
    {
        return $this->userIssuance;
    }

    /**
     * @param mixed $userIssuance
     */
    public function setUserIssuance($userIssuance): void
    {
        $this->userIssuance = $userIssuance;
    }

    /**
     * @return mixed
     */
    public function getisActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param $password
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return mixed
     */
    public function getFavoritesBookCopy()
    {
        return $this->favoritesBookCopy;
    }

    /**
     * @param mixed $favoritesBookCopy
     */
    public function setFavoritesBookCopy($favoritesBookCopy): void
    {
        $this->favoritesBookCopy = $favoritesBookCopy;
    }

    /**
     * @return null|string
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
        ));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }
}