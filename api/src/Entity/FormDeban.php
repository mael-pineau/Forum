<?php

namespace App\Entity;

use App\Repository\FormDebanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormDebanRepository::class)]
class FormDeban
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $message;

    #[ORM\Column(type: 'integer')]
    private $date;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private $user;

    #[ORM\Column(type: 'boolean', nullable: true,  options: ["default" => null])]
    private $is_refused;

    #[ORM\OneToOne(/*inversedBy: 'formDeban',*/ targetEntity: Ban::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable : false, onDelete: "CASCADE")]
    private $ban;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIsRefused(): ?bool
    {
        return $this->is_refused;
    }

    public function setIsRefused(?bool $is_refused): self
    {
        $this->is_refused = $is_refused;

        return $this;
    }

    public function getBan(): ?Ban
    {
        return $this->ban;
    }

    public function setBan(?Ban $ban): self
    {
        $this->ban = $ban;

        return $this;
    }
}
