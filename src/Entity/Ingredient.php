<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name_many = null;

    /**
     * @var Collection<int, Quantity>
     */
    #[ORM\OneToMany(targetEntity: Quantity::class, mappedBy: 'ingredient')]
    private Collection $ingredient;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, UserCreateIngredient>
     */
    #[ORM\OneToMany(targetEntity: UserCreateIngredient::class, mappedBy: 'ingredient')]
    private Collection $userCreateIngredients;

    public function __construct()
    {
        $this->ingredient = new ArrayCollection();
        $this->userCreateIngredients = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getNameMany(): ?string
    {
        return $this->name_many;
    }

    public function setNameMany(?string $name_many): static
    {
        $this->name_many = $name_many;

        return $this;
    }

    /**
     * @return Collection<int, Quantity>
     */
    public function getIngredient(): Collection
    {
        return $this->ingredient;
    }

    public function addIngredient(Quantity $ingredient): static
    {
        if (!$this->ingredient->contains($ingredient)) {
            $this->ingredient->add($ingredient);
            $ingredient->setIngredient($this);
        }

        return $this;
    }

    public function removeIngredient(Quantity $ingredient): static
    {
        if ($this->ingredient->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getIngredient() === $this) {
                $ingredient->setIngredient(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, UserCreateIngredient>
     */
    public function getUserCreateIngredients(): Collection
    {
        return $this->userCreateIngredients;
    }

    public function addUserCreateIngredient(UserCreateIngredient $userCreateIngredient): static
    {
        if (!$this->userCreateIngredients->contains($userCreateIngredient)) {
            $this->userCreateIngredients->add($userCreateIngredient);
            $userCreateIngredient->setIngredient($this);
        }

        return $this;
    }

    public function removeUserCreateIngredient(UserCreateIngredient $userCreateIngredient): static
    {
        if ($this->userCreateIngredients->removeElement($userCreateIngredient)) {
            // set the owning side to null (unless already changed)
            if ($userCreateIngredient->getIngredient() === $this) {
                $userCreateIngredient->setIngredient(null);
            }
        }

        return $this;
    }

}
