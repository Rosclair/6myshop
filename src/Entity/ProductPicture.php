<?php

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\UpdatedAtTrait;
use App\Repository\ProductPictureRepository;
use Symfony\Component\Validator\Constraints as  Assert;

#[ORM\Entity(repositoryClass: ProductPictureRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ProductPicture
{
    use IdTrait;
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 255)]
    private ?string $urlName = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

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

    public function getUrlName(): ?string
    {
        return $this->urlName;
    }

    public function setUrlName(string $urlName): static
    {
        $this->urlName = $urlName;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
