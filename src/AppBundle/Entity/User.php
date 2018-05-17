<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=127, nullable=false, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="text", nullable=true, unique=false)
     */
    private $avatar;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime")
     */
    private $datetime;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_action_time", type="datetime")
     */
    private $lastActionTime;

    /**
     * @var Messages[]
     *
     * @ORM\OneToMany(targetEntity="Messages", mappedBy="user")
     */
    private $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return User
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param Messages[] $messages
     * @return User
     */
    public function setMessages(array $messages): User
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @return Messages[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param \DateTime $lastActionTime
     * @return User
     */
    public function setLastActionTime(\DateTime $lastActionTime): User
    {
        $this->lastActionTime = $lastActionTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastActionTime(): \DateTime
    {
        return $this->lastActionTime;
    }

}
