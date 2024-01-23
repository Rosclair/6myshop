<?php

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\UpdatedAtTrait;
use App\Repository\ShopValidationRepository;
use Symfony\Component\Validator\Constraints as  Assert;

#[ORM\Entity(repositoryClass: ShopValidationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ShopValidation
{
    use IdTrait;
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 255)]
    private ?string $decision = null;

    #[ORM\Column]
    private ?bool $visibility = null;

    #[ORM\ManyToOne(inversedBy: 'shopValidations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\OneToOne(inversedBy: 'shopValidation', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shop $shop = null;

    #[Assert\NotBlank()]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * Permet d'initialiser la date de creation et de mise a jour lors de la creation !
     */
    #[ORM\PrePersist]
    public function initializePrePersist()
    {
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * Permet d'initialiser la date de mise a jour lors de ma mise à jour !
     */
    #[ORM\PreUpdate]
    public function initializeUpdatedAt()
    {
        $this->updatedAt = new \DateTime('now');
    }

    public function getDecision(): ?string
    {
        return $this->decision;
    }

    public function setDecision(string $decision): static
    {
        $this->decision = $decision;

        return $this;
    }

    public function isVisibility(): ?bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
