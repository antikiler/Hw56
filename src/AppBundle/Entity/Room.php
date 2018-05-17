<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Room
 *
 * @ORM\Table(name="room")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoomRepository")
 */
class Room
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
     * @ORM\Column(name="name", type="string", length=127, nullable=false, unique=true)
     */
    private $name;

    /**
     * @var Messages[]
     *
     * @ORM\OneToMany(targetEntity="Messages", mappedBy="room")
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
     * Set name
     *
     * @param string $name
     *
     * @return Room
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Messages[] $messages
     * @return Room
     */
    public function setMessages(array $messages): Room
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @return Messages[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

}
