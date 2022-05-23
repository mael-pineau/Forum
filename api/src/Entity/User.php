<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $mail;

    #[ORM\Column(type: 'string', length: 255)]
    private $username;

    #[ORM\Column(type: 'text')]
    private $password;

    #[ORM\Column(type: 'string', length: 255, nullable: false, options: ["default" => "default.jpeg"])]
    private $image;

    #[ORM\Column(type: 'boolean', nullable: false, options: ["default" => 0])]
    private $is_admin;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Subject::class, /*orphanRemoval: false*/)]
    #[ORM\JoinColumn(nullable: true)]
    private $subjects;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, /*orphanRemoval: false*/)]
    #[ORM\JoinColumn(nullable: true)]
    private $comments;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Ban::class)]
    private $ban;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: FormDeban::class)]
    private $formDeban;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->is_admin;
    }

    public function setIsAdmin(bool $is_admin): self
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): self
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects[] = $subject;
            $subject->setUser($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): self
    {
        if ($this->subjects->removeElement($subject)) {
            // set the owning side to null (unless already changed)
            if ($subject->getUser() === $this) {
                $subject->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

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

    public function getFormDeban(): ?FormDeban
    {
        return $this->formDeban;
    }

    public function setFormDeban(?FormDeban $formDeban): self
    {
        // unset the owning side of the relation if necessary
        if ($formDeban === null && $this->formDeban !== null) {
            $this->formDeban->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($formDeban !== null && $formDeban->getUser() !== $this) {
            $formDeban->setUser($this);
        }

        $this->formDeban = $formDeban;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }
}
