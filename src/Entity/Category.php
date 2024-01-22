<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use App\Entity\Trait\IdTrait;
use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Category
{
    use IdTrait;
    use SlugTrait;

    /*#[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;*/

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'category', targetEntity: self::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $parent;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private $category;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Shop::class, orphanRemoval: true)]
    private Collection $shops;

    #[ORM\OneToMany(mappedBy: 'sousCategory', targetEntity: Product::class, orphanRemoval: true)]
    private Collection $products;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->shops = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    /**
     * Permet d'initialiser le slug!
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initialize()
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
    }

    /*public function getId(): ?int
    {
        return $this->id;
    }*/

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

        /**
     * @return Collection|self[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addSousCategory(self $sousCategory): self
    {
        if (!$this->category->contains($sousCategory)) {
            $this->category[] = $sousCategory;
            $sousCategory->setParent($this);
        }

        return $this;
    }

    public function removeSousCategory(self $sousCategory): self
    {
        if ($this->category->removeElement($sousCategory)) {
            // set the owning side to null (unless already changed)
            if ($sousCategory->getParent() === $this) {
                $sousCategory->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Shop>
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addShop(Shop $shop): static
    {
        if (!$this->shops->contains($shop)) {
            $this->shops->add($shop);
            $shop->setCategory($this);
        }

        return $this;
    }

    public function removeShop(Shop $shop): static
    {
        if ($this->shops->removeElement($shop)) {
            // set the owning side to null (unless already changed)
            if ($shop->getCategory() === $this) {
                $shop->setCategory(null);
            }
        }

        return $this;
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
            $product->setSousCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSousCategory() === $this) {
                $product->setSousCategory(null);
            }
        }

        return $this;
    }
}
