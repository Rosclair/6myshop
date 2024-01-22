<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use App\Entity\Trait\IdTrait;
use Doctrine\DBAL\Types\Types;
use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\UpdatedAtTrait;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as  Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Product
{
    use IdTrait;
    use CreatedAtTrait;
    use UpdatedAtTrait;
    use SlugTrait;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $firstPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $priceOfShip = null;

    #[ORM\Column]
    private ?int $lastPrice = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?bool $onSale = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shop $shop = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $sousCategory = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductPicture::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }


    /**
     * Permet d'initialiser le slug et la date de creation et de mise a jour!
     */
    #[ORM\PrePersist]
    public function initializePrePersist()
    {
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');

        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
        $this->onSale = 0;
    }

    /**
     * Permet d'initialiser le slug et la date de mise a jour!
     */
    #[ORM\PreUpdate]
    public function initializePreUpdate()
    {
        $this->updatedAt = new \DateTime('now');

        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getFirstPrice(): ?int
    {
        return $this->firstPrice;
    }

    public function setFirstPrice(?int $firstPrice): static
    {
        $this->firstPrice = $firstPrice;

        return $this;
    }

    public function getPriceOfShip(): ?int
    {
        return $this->priceOfShip;
    }

    public function setPriceOfShip(?int $priceOfShip): static
    {
        $this->priceOfShip = $priceOfShip;

        return $this;
    }

    public function getLastPrice(): ?int
    {
        return $this->lastPrice;
    }

    public function setLastPrice(int $lastPrice): static
    {
        $this->lastPrice = $lastPrice;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function isOnSale(): ?bool
    {
        return $this->onSale;
    }

    public function setOnSale(bool $onSale): static
    {
        $this->onSale = $onSale;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    public function getSousCategory(): ?Category
    {
        return $this->sousCategory;
    }

    public function setSousCategory(?Category $sousCategory): static
    {
        $this->sousCategory = $sousCategory;

        return $this;
    }  

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, ProductPicture>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductPicture $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(ProductPicture $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

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
}
