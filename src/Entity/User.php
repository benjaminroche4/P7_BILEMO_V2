<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get:infos", "get:userList"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get:infos", "get:userList", "post:user"})
     * @Assert\NotBlank(message="The first name can't not be blank")
     * @Assert\Length(min=3, max=50)
     */
    private $fisrtname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get:infos", "get:userList", "post:user"})
     * @Assert\NotBlank(message="The last name can't not be blank")
     * @Assert\Length(min=3, max=50)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get:infos", "get:userList", "post:user"})
     * @Assert\NotBlank(message="The email can't not be blank")
     * @Assert\Length(min=3, max=100)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"get:infos", "post:user"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="users")
     * @Groups({"get:infos", "post:user"})
     * @Assert\NotBlank(message="The customerId can't not be blank")
     */
    private $customerId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFisrtname(): ?string
    {
        return $this->fisrtname;
    }

    public function setFisrtname(string $fisrtname): self
    {
        $this->fisrtname = $fisrtname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCustomerId(): ?Customer
    {
        return $this->customerId;
    }

    public function setCustomerId(?Customer $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }
}
