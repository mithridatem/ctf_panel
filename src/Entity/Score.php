<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateScore = null;

    #[ORM\ManyToOne(inversedBy: 'scores')]
    private ?Participant $participant = null;

    #[ORM\ManyToMany(targetEntity: Flag::class)]
    private Collection $flags;

    public function __construct()
    {
        $this->flags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateScore(): ?\DateTimeInterface
    {
        return $this->dateScore;
    }

    public function setDateScore(\DateTimeInterface $dateScore): static
    {
        $this->dateScore = $dateScore;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): static
    {
        $this->participant = $participant;

        return $this;
    }

    /**
     * @return Collection<int, Flag>
     */
    public function getFlags(): Collection
    {
        return $this->flags;
    }

    public function addFlag(Flag $flag): static
    {
        if (!$this->flags->contains($flag)) {
            $this->flags->add($flag);
        }

        return $this;
    }

    public function removeFlag(Flag $flag): static
    {
        $this->flags->removeElement($flag);

        return $this;
    }
}
