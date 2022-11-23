<?php

namespace App\Entity;

use App\Repository\ResumeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResumeRepository::class)]
class Resume
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $position = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filePath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateUpdate = null;

    #[ORM\OneToMany(mappedBy: 'resume', targetEntity: Reaction::class)]
    private Collection $reactions;

    #[ORM\OneToMany(mappedBy: 'resume', targetEntity: SendResume::class)]
    private Collection $sendResumes;

    public function __construct()
    {
        $this->reactions = new ArrayCollection();
        $this->sendResumes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    public function setDateCreate(\DateTimeInterface $dateCreate): self
    {
        if (!$this->getDateUpdate()) {
            $this->dateCreate = new \DateTime();
        } else {
            $this->dateCreate = $dateCreate;
        }
        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(\DateTimeInterface $dateUpdate): self
    {
        $this->dateUpdate = new \DateTime();

        return $this;
    }

    /**
     * @return Collection<int, Reaction>
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function addReaction(Reaction $reaction): self
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions->add($reaction);
            $reaction->setResume($this);
        }

        return $this;
    }

    public function removeReaction(Reaction $reaction): self
    {
        if ($this->reactions->removeElement($reaction)) {
            // set the owning side to null (unless already changed)
            if ($reaction->getResume() === $this) {
                $reaction->setResume(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SendResume>
     */
    public function getSendResumes(): Collection
    {
        return $this->sendResumes;
    }

    public function addSendResume(SendResume $sendResume): self
    {
        if (!$this->sendResumes->contains($sendResume)) {
            $this->sendResumes->add($sendResume);
            $sendResume->setResume($this);
        }

        return $this;
    }

    public function removeSendResume(SendResume $sendResume): self
    {
        if ($this->sendResumes->removeElement($sendResume)) {
            // set the owning side to null (unless already changed)
            if ($sendResume->getResume() === $this) {
                $sendResume->setResume(null);
            }
        }

        return $this;
    }
}
