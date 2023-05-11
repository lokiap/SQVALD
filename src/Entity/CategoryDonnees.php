<?php

namespace App\Entity;

use App\Repository\CategoryDonneesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryDonneesRepository::class)
 */
class CategoryDonnees
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
/*
    /**
     * @ORM\OneToMany(targetEntity=DonneesType::class, mappedBy="categorydonnees")
     */
    //private $donneesTypes;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="categorydonnees")
     */
    private $documents;

    public function __construct()
    {
       // $this->donneesTypes = new ArrayCollection();
        $this->documents = new ArrayCollection();
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
    public function __toString(){
        return $this->name;
    }
/*
    /**
     * @return Collection|DonneesType[]
     */
    /*
    public function getDonneesTypes(): Collection
    {
        return $this->donneesTypes;
    }

    public function addDonneesType(DonneesType $donneesType): self
    {
        if (!$this->donneesTypes->contains($donneesType)) {
            $this->donneesTypes[] = $donneesType;
            $donneesType->setCategorydonnees($this);
        }

        return $this;
    }

    public function removeDonneesType(DonneesType $donneesType): self
    {
        if ($this->donneesTypes->removeElement($donneesType)) {
            // set the owning side to null (unless already changed)
            if ($donneesType->getCategorydonnees() === $this) {
                $donneesType->setCategorydonnees(null);
            }
        }

        return $this;
    }
    */

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setCategorydonnees($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getCategorydonnees() === $this) {
                $document->setCategorydonnees(null);
            }
        }

        return $this;
    }
}
