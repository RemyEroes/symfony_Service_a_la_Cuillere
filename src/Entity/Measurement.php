<?php

namespace App\Entity;

use App\Repository\MeasurementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeasurementRepository::class)]
class Measurement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Quantity>
     */
    #[ORM\OneToMany(targetEntity: Quantity::class, mappedBy: 'measurement')]
    private Collection $measurement;

    public function __construct()
    {
        $this->measurement = new ArrayCollection();
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

    /**
     * @return Collection<int, Quantity>
     */
    public function getMeasurement(): Collection
    {
        return $this->measurement;
    }

    public function addMeasurement(Quantity $measurement): static
    {
        if (!$this->measurement->contains($measurement)) {
            $this->measurement->add($measurement);
            $measurement->setMeasurement($this);
        }

        return $this;
    }

    public function removeMeasurement(Quantity $measurement): static
    {
        if ($this->measurement->removeElement($measurement)) {
            // set the owning side to null (unless already changed)
            if ($measurement->getMeasurement() === $this) {
                $measurement->setMeasurement(null);
            }
        }

        return $this;
    }
}
