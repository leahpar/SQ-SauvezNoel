<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $message;

    /**
     * @ORM\Column(type="datetime")
     */
    public \DateTime $date;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    public int $accepted = 0;

    public function valid(): bool
    {
        return !empty($this->nom) && !empty($this->message);
    }
}
