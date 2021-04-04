<?php

namespace App\Entity;

use App\Repository\UsersSectorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsersSectorRepository::class)
 */
class UsersSector
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=Sectors::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $sectors;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getSectors(): ?Sectors
    {
        return $this->sectors;
    }

    public function setSectors(?Sectors $sectors): self
    {
        $this->sectors = $sectors;

        return $this;
    }
}
