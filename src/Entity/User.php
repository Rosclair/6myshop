<?php

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\UpdatedAtTrait;
use Doctrine\Common\Collections\Collection;
use App\Repository\ShopValidationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as  Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(
    fields: ['email'],
    message: "Un utilisateur avec cet email existe déjà !",
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /*#[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;*/

    use IdTrait;
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[Assert\NotBlank()]
    #[Assert\Length(
        min: 5,
        minMessage: "Top Court! Ne doit pas être inférieur à {{ limit }} cararacters",
    )]
    #[ORM\Column(length: 255)]
    private ?string $fullname = null;

    /**
     * @var string The hashed password
     */
    #[RollerworksPassword\PasswordRequirements(
        requireLetters:	true,
        requireCaseDiff: true,
        requireNumbers:	true,
        requireSpecialCharacter: true,
        missingLettersMessage: "Doit contenir au moins une lettre.",
        requireCaseDiffMessage: "Doit inclure des lettres majuscules et minuscules.",
        missingNumbersMessage: "Doit inclure au moins un chiffre.",
        missingSpecialCharacterMessage:	'Doit contenir au moins un caractère spécial.',
    )]
    #[RollerworksPassword\PasswordStrength(
        minLength:8,
        minStrength:4,
        tooShortMessage: "Doit comporter au moins {{length}} caractères.",
        message: 'Trop faible!'
    )]
    #[ORM\Column]    
    private ?string $password = null;

    private ?string $plainPassword = null;
    
    #[Assert\EqualTo(
        propertyPath:"password",
        message:"Vous n'avez pas correctement confirmé votre mot de passe"
    )]
    public $passwordConfirm;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Shop::class, orphanRemoval: true)]
    private Collection $shops;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Product::class, orphanRemoval: true)]
    private Collection $products;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: ShopValidation::class)]
    private Collection $shopValidations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->shopValidations = new ArrayCollection();
    }

    /**
     * Permet d'initialiser date de creation et de mise a jour!
     */
    #[ORM\PrePersist]
    public function initializePrePersist()
    {
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');
        $this->roles = ['ROLE_USER'];
    }

    /**
     * Permet d'initialiser la date de mise a jour!
     */
    #[ORM\PreUpdate]
    public function initializePreUpdate()
    {
        $this->updatedAt = new \DateTime('now');
    }

    /*public function getId(): ?int
    {
        return $this->id;
    }*/

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->getEmail();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function __toString()
    {
        return $this->email;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
 
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

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
            $shop->setAuthor($this);
        }

        return $this;
    }

    public function removeShop(Shop $shop): static
    {
        if ($this->shops->removeElement($shop)) {
            // set the owning side to null (unless already changed)
            if ($shop->getAuthor() === $this) {
                $shop->setAuthor(null);
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
            $product->setAuthor($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getAuthor() === $this) {
                $product->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShopValidation>
     */
    public function getShopValidations(): Collection
    {
        return $this->shopValidations;
    }

    public function addShopValidation(ShopValidation $shopValidation): static
    {
        if (!$this->shopValidations->contains($shopValidation)) {
            $this->shopValidations->add($shopValidation);
            $shopValidation->setAuthor($this);
        }

        return $this;
    }

    public function removeShopValidation(ShopValidation $shopValidation): static
    {
        if ($this->shopValidations->removeElement($shopValidation)) {
            // set the owning side to null (unless already changed)
            if ($shopValidation->getAuthor() === $this) {
                $shopValidation->setAuthor(null);
            }
        }

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
