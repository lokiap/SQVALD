<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PartnerRepository::class)
 * @Vich\Uploadable
 */
class Partner
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $illustration;

	/**
	 * @Vich\UploadableField(mapping="partner_image", fileNameProperty="illustration")
	 * @var File
	 */
	private $illustrationFile;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $btnUrl;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	private $updateAt;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="partner")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIllustration(): ?string
    {
        return $this->illustration;
    }

    public function setIllustration(?string $illustration): self
    {
        $this->illustration = $illustration;

        return $this;
    }

    public function getBtnUrl(): ?string
    {
        return $this->btnUrl;
    }

    public function setBtnUrl(?string $btnUrl): self
    {
        $this->btnUrl = $btnUrl;

        return $this;
    }

	public function getUpdateAt(): \DateTime
                        	{
                        		return $this->updateAt;
                        	}

	public function setUpdateAt(\DateTime $updateAt): void
                        	{
                        		$this->updateAt = $updateAt;
                        	}

	public function getIllustrationFile(): File
                        	{
                        		return $this->illustrationFile;
                        	}

	public function setIllustrationFile(File $illustrationFile = null): void
                        	{
                        		$this->illustrationFile = $illustrationFile;
                        		if ($illustrationFile) $this->updateAt = new \DateTime('now');
                        	}

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setPartner($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getPartner() === $this) {
                $user->setPartner(null);
            }
        }

        return $this;
    }
}