<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $recipe_text = null;

    #[ORM\Column(length: 255)]
    private ?string $duration = null;

    #[ORM\Column]
    private ?int $people = null;

    /**
     * @var Collection<int, Quantity>
     */
    #[ORM\OneToMany(targetEntity: Quantity::class, mappedBy: 'recipe')]
    private Collection $quantities;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Favorite>
     */
    #[ORM\OneToMany(targetEntity: Favorite::class, mappedBy: 'recipe')]
    private Collection $favorites;

    /**
     * @var Collection<int, UserFavorite>
     */
    #[ORM\OneToMany(targetEntity: UserFavorite::class, mappedBy: 'recipe')]
    private Collection $userFavorites;

    /**
     * @var Collection<int, UserCreateRecipe>
     */
    #[ORM\OneToMany(targetEntity: UserCreateRecipe::class, mappedBy: 'recipe')]
    private Collection $userCreateRecipes;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'recipe')]
    private Collection $commentaires;

    public function __construct()
    {
        $this->quantities = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->userFavorites = new ArrayCollection();
        $this->userCreateRecipes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getRecipeText(): ?string
    {
        return $this->recipe_text;
    }

    public function setRecipeText(string $recipe_text): static
    {
        $this->recipe_text = $recipe_text;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPeople(): ?int
    {
        return $this->people;
    }

    public function setPeople(int $people): static
    {
        $this->people = $people;

        return $this;
    }

    /**
     * @return Collection<int, Quantity>
     */
    public function getQuantities(): Collection
    {
        return $this->quantities;
    }

    public function addQuantity(Quantity $quantity): static
    {
        if (!$this->quantities->contains($quantity)) {
            $this->quantities->add($quantity);
            $quantity->setRecipe($this);
        }

        return $this;
    }

    public function removeQuantity(Quantity $quantity): static
    {
        if ($this->quantities->removeElement($quantity)) {
            // set the owning side to null (unless already changed)
            if ($quantity->getRecipe() === $this) {
                $quantity->setRecipe(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setRecipe($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getRecipe() === $this) {
                $favorite->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserFavorite>
     */
    public function getUserFavorites(): Collection
    {
        return $this->userFavorites;
    }

    public function addUserFavorite(UserFavorite $userFavorite): static
    {
        if (!$this->userFavorites->contains($userFavorite)) {
            $this->userFavorites->add($userFavorite);
            $userFavorite->setRecipe($this);
        }

        return $this;
    }

    public function removeUserFavorite(UserFavorite $userFavorite): static
    {
        if ($this->userFavorites->removeElement($userFavorite)) {
            // set the owning side to null (unless already changed)
            if ($userFavorite->getRecipe() === $this) {
                $userFavorite->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserCreateRecipe>
     */
    public function getUserCreateRecipes(): Collection
    {
        return $this->userCreateRecipes;
    }

    public function addUserCreateRecipe(UserCreateRecipe $userCreateRecipe): static
    {
        if (!$this->userCreateRecipes->contains($userCreateRecipe)) {
            $this->userCreateRecipes->add($userCreateRecipe);
            $userCreateRecipe->setRecipe($this);
        }

        return $this;
    }

    public function removeUserCreateRecipe(UserCreateRecipe $userCreateRecipe): static
    {
        if ($this->userCreateRecipes->removeElement($userCreateRecipe)) {
            // set the owning side to null (unless already changed)
            if ($userCreateRecipe->getRecipe() === $this) {
                $userCreateRecipe->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setRecipe($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getRecipe() === $this) {
                $commentaire->setRecipe(null);
            }
        }

        return $this;
    }
}
