<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $site = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: SendResume::class)]
    private Collection $sendResumes;

    public function __construct()
    {
        $this->sendResumes = new ArrayCollection();
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

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(string $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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
            $sendResume->setCompany($this);
        }

        return $this;
    }

    public function removeSendResume(SendResume $sendResume): self
    {
        if ($this->sendResumes->removeElement($sendResume)) {
            // set the owning side to null (unless already changed)
            if ($sendResume->getCompany() === $this) {
                $sendResume->setCompany(null);
            }
        }

        return $this;
    }
}
