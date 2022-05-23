<?php

namespace App\Entity;

use App\Repository\BanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BanRepository::class)]
class Ban
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $date;

    #[ORM\Column(type: 'boolean', nullable: false, options: ["default" => 0])]
    private $is_permanent;

    #[ORM\Column(type: 'text', nullable: true)]
    private $reason;

    #[ORM\Column(type: 'integer', nullable: true, options: ["default" => null])]
    private $dateDeban;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIsPermanent(): ?bool
    {
        return $this->is_permanent;
    }

    public function setIsPermanent(bool $is_permanent): self
    {
        $this->is_permanent = $is_permanent;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getDateDeban(): ?int
    {
        return $this->dateDeban;
    }

    public function setDateDeban(int $dateDeban): self
    {
        $this->dateDeban = $dateDeban;

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
}
