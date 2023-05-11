<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @UniqueEntity(fields="title", message="Un article du même nom existe déjà")
 * @Vich\Uploadable
 */
class Document
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resume;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $picture;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    /**
     * @return mixed
     */
    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    /**
     * @param mixed $updateAt
     */
    public function setUpdateAt(?\DateTimeInterface $updateAt): void
    {
        $this->updateAt = $updateAt;
    }


    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="documents")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryDonnees::class, inversedBy="documents")
     */
    private $categorydonnees;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $brochureFilename;

    public function __construct()
    {
        $this->author = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updateAt = new \DateTime();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }



    /**
     * @return Collection|User[]
     */
    public function getAuthor(): Collection
    {
        return $this->author;
    }

    public function addAuthor(User $author): self
    {
        if (!$this->author->contains($author)) {
            $this->author[] = $author;
        }

        return $this;
    }

    public function removeAuthor(User $author): self
    {
        $this->author->removeElement($author);

        return $this;
    }

    public function getCategorydonnees(): ?CategoryDonnees
    {
        return $this->categorydonnees;
    }

    public function setCategorydonnees(?CategoryDonnees $categorydonnees): self
    {
        $this->categorydonnees = $categorydonnees;

        return $this;
    }


    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }


   public function getBrochureFilename(): ?string
   {
       return $this->brochureFilename;
   }

   public function setBrochureFilename(?string $brochureFilename): self
   {
       $this->brochureFilename = $brochureFilename;

       return $this;
   }


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="images_directory", fileNameProperty="picture")
     *
     * @var File|null
     * @Assert\Image(maxSize="8M")
     */
    private $imageFile;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdateAt(new \DateTimeImmutable);
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @Vich\UploadableField(mapping="images_directory", fileNameProperty="brochureFilename")
     * @var File
     */
    private $brochureFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable="true")
     */
    private $content;

    /**
     * @return File
     */
    public function getBrochureFile(): ?File
    {
        return $this->brochureFile;
    }

	/**
	 * @param File|null $brochureFile
	 */
    public function setBrochureFile(?File $brochureFile): void
    {
        $this->brochureFile = $brochureFile;
        if (null !== $brochureFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdateAt(new \DateTimeImmutable);
        }
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

	public function getSummary(): string
	{
		if ($this->resume != null && $this->resume != '') {
			return $this->resume;
		}
		return htmlspecialchars_decode(substr(strip_tags($this->content), 0, 254).'…', ENT_QUOTES);
	}
}