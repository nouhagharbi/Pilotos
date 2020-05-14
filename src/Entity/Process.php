<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ProcessRepository")
 */
class Process
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $performance_indicator;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pilot;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Objective", mappedBy="objective_process")
     */
    private $objectives;

    public function __construct()
    {
        $this->objectives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPerformanceIndicator(): ?string
    {
        return $this->performance_indicator;
    }

    public function setPerformanceIndicator(string $performance_indicator): self
    {
        $this->performance_indicator = $performance_indicator;

        return $this;
    }

    public function getPilot(): ?string
    {
        return $this->pilot;
    }

    public function setPilot(string $pilot): self
    {
        $this->pilot = $pilot;

        return $this;
    }

    /**
     * @return Collection|Objective[]
     */
    public function getObjectives(): Collection
    {
        return $this->objectives;
    }

    public function addObjective(Objective $objective): self
    {
        if (!$this->objectives->contains($objective)) {
            $this->objectives[] = $objective;
            $objective->addObjectiveProcess($this);
        }

        return $this;
    }

    public function removeObjective(Objective $objective): self
    {
        if ($this->objectives->contains($objective)) {
            $this->objectives->removeElement($objective);
            $objective->removeObjectiveProcess($this);
        }

        return $this;
    }
}
