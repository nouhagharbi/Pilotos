<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ObjectiveRepository")
 */
class Objective
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
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $timelimit;

    // /**
    //  * @ORM\ManyToMany(targetEntity="App\Entity\Enjeu", inversedBy="objectives")
    //  */
    // private $enjeux_objective;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Process", inversedBy="objectives")
     */
    private $objective_process;

    /**
     * @ORM\Column(type="boolean")
     */
    private $predefined_indicator;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $performance_indicator;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $objective_to_acheive;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $initial_state_indicator;

    /**
     * @ORM\Column(type="integer")
     */
    private $action_number;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $current_state_indicator;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $advancement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $current_state;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $comments;

    public function __construct()
    {
        // $this->enjeux_objective = new ArrayCollection();
        $this->objective_process = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function gettimelimit(): ?\DateTimeInterface
    {
        return $this->timelimit;
    }

    public function settimelimit(\DateTimeInterface $timelimit): self
    {
        $this->timelimit = $timelimit;

        return $this;
    }

    // /**
    //  * @return Collection|enjeu[]
    //  */
    // public function getEnjeuxObjective(): Collection
    // {
    //     return $this->enjeux_objective;
    // }

    // public function addEnjeuxObjective(enjeu $enjeuxObjective): self
    // {
    //     if (!$this->enjeux_objective->contains($enjeuxObjective)) {
    //         $this->enjeux_objective[] = $enjeuxObjective;
    //     }

    //     return $this;
    // }

    // public function removeEnjeuxObjective(enjeu $enjeuxObjective): self
    // {
    //     if ($this->enjeux_objective->contains($enjeuxObjective)) {
    //         $this->enjeux_objective->removeElement($enjeuxObjective);
    //     }

    //     return $this;
    // }

    /**
     * @return Collection|process[]
     */
    public function getObjectiveProcess(): Collection
    {
        return $this->objective_process;
    }

    public function addObjectiveProcess(process $objectiveProcess): self
    {
        if (!$this->objective_process->contains($objectiveProcess)) {
            $this->objective_process[] = $objectiveProcess;
        }

        return $this;
    }

    public function removeObjectiveProcess(process $objectiveProcess): self
    {
        if ($this->objective_process->contains($objectiveProcess)) {
            $this->objective_process->removeElement($objectiveProcess);
        }

        return $this;
    }

    public function getPredefinedIndicator(): ?bool
    {
        return $this->predefined_indicator;
    }

    public function setPredefinedIndicator(bool $predefined_indicator): self
    {
        $this->predefined_indicator = $predefined_indicator;

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

    public function getObjectiveToAcheive(): ?string
    {
        return $this->objective_to_acheive;
    }

    public function setObjectiveToAcheive(string $objective_to_acheive): self
    {
        $this->objective_to_acheive = $objective_to_acheive;

        return $this;
    }

    public function getInitialStateIndicator(): ?string
    {
        return $this->initial_state_indicator;
    }

    public function setInitialStateIndicator(string $initial_state_indicator): self
    {
        $this->initial_state_indicator = $initial_state_indicator;

        return $this;
    }

    public function getActionNumber(): ?int
    {
        return $this->action_number;
    }

    public function setActionNumber(int $action_number): self
    {
        $this->action_number = $action_number;

        return $this;
    }

    public function getCurrentStateIndicator(): ?string
    {
        return $this->current_state_indicator;
    }

    public function setCurrentStateIndicator(string $current_state_indicator): self
    {
        $this->current_state_indicator = $current_state_indicator;

        return $this;
    }

    public function getAdvancement(): ?string
    {
        return $this->advancement;
    }

    public function setAdvancement(string $advancement): self
    {
        $this->advancement = $advancement;

        return $this;
    }

    public function getCurrentState(): ?string
    {
        return $this->current_state;
    }

    public function setCurrentState(string $current_state): self
    {
        $this->current_state = $current_state;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }
}
