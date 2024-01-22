<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use App\Entity\Trait\IdTrait;
use App\Entity\Trait\SlugTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ShopRepository;
use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\UpdatedAtTrait;
use Symfony\Component\Validator\Constraints as  Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ShopRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: ['name'],
    message: "Un boutique avec ce nom existe déjà !",
)]
class Shop
{
    use IdTrait;
    use CreatedAtTrait;
    use UpdatedAtTrait;
    use SlugTrait;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $contact = null;

    #[ORM\Column(length: 255)]
    private ?string $district = null;

    #[ORM\Column(length: 255)]
    private ?string $shopPictureName = null;

    #[ORM\ManyToOne(inversedBy: 'shops')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'shops')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\ManyToOne(inversedBy: 'shops')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'shop', targetEntity: Product::class, orphanRemoval: true)]
    private Collection $products;

    #[ORM\OneToOne(mappedBy: 'shop', cascade: ['persist', 'remove'])]
    private ?ShopValidation $shopValidation = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContact(): ?int
    {
        return $this->contact;
    }

    public function setContact(int $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(string $district): static
    {
        $this->district = $district;

        return $this;
    }

    public function getShopPictureName(): ?string
    {
        return $this->shopPictureName;
    }

    public function setShopPictureName(?string $shopPictureName): self
    {
        $this->shopPictureName = $shopPictureName;

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

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setShop($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getShop() === $this) {
                $product->setShop(null);
            }
        }

        return $this;
    }

    public function getShopValidation(): ?ShopValidation
    {
        return $this->shopValidation;
    }

    public function setShopValidation(ShopValidation $shopValidation): static
    {
        // set the owning side of the relation if necessary
        if ($shopValidation->getShop() !== $this) {
            $shopValidation->setShop($this);
        }

        $this->shopValidation = $shopValidation;

        return $this;
    }
}
