<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 * @Vich\Uploadable
 */
class Video
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $link;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $src;

	/**
	 * @Vich\UploadableField(mapping="user_video", fileNameProperty="src")
	 * @var File
	 */
	private $srcFile;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	private $updateAt;

	/**
	 * @ORM\ManyToMany(targetEntity=User::class, inversedBy="videos")
	 */
	private $authors;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $title;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $isActive = false;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $slug;

	public function __construct()
	{
		$this->authors = new ArrayCollection();
		$this->createdAt = new \DateTime();
		$this->updateAt = new \DateTime();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getLink(): ?string
	{
		return $this->link;
	}

	public function setLink(?string $link): self
	{
		$this->link = $link;

		return $this;
	}

	public function getSrc(): ?string
	{
		return $this->src;
	}

	public function setSrc(?string $src): self
	{
		$this->src = $src;

		return $this;
	}

	/**
	 * @return File
	 */
	public function getSrcFile(): ?File
	{
		return $this->srcFile;
	}

	/**
	 * @param File $srcFile
	 */
	public function setSrcFile(File $srcFile = null): void
	{
		$this->srcFile = $srcFile;
		if ($srcFile) $this->updateAt = new \DateTime('now');
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdateAt(): \DateTime
	{
		return $this->updateAt;
	}

	/**
	 * @param \DateTime $updateAt
	 */
	public function setUpdateAt(\DateTime $updateAt): void
	{
		$this->updateAt = $updateAt;
	}

	/**
	 * @return Collection<int, User>
	 */
	public function getAuthors(): Collection
	{
		return $this->authors;
	}

	public function addAuthor(User $author): self
	{
		if (!$this->authors->contains($author)) {
			$this->authors[] = $author;
		}

		return $this;
	}

	public function removeAuthor(User $author): self
	{
		$this->authors->removeElement($author);

		return $this;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(string $title): self
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	/**
	 * @param mixed $createdAt
	 */
	public function setCreatedAt($createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->isActive;
	}

	/**
	 * @param bool $isActive
	 */
	public function setIsActive(bool $isActive): void
	{
		$this->isActive = $isActive;
	}

	/**
	 * @return mixed
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @param mixed $slug
	 */
	public function setSlug($slug): void
	{
		$this->slug = $slug;
	}

	public function getType(): string
	{
		return $this->src == null ? "Externe" : "Interne";
	}
}
