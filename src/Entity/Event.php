<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
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
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $resume;

	/**
	 * @ORM\Column(type="date")
	 */
	private $dateBegin;

	/**
	 * @ORM\Column(type="date")
	 */
	private $dateEnd;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $place;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $isActive = false;

	/**
	 * @ORM\ManyToOne(targetEntity=CategoryNews::class, inversedBy="events")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $category;

	/**
	 * @ORM\ManyToMany(targetEntity=User::class, inversedBy="events")
	 */
	private $author;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $content;

	/**
	 * @ORM\Column(type="date")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $slug;

	public function __construct()
	{
		$this->author = new ArrayCollection();
		$this->createdAt = new \DateTime();
	}

	public function getId(): ?int
	{
		return $this->id;
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

	public function getResume(): ?string
	{
		return $this->resume;
	}

	public function setResume(?string $resume): self
	{
		$this->resume = $resume;

		return $this;
	}

	public function getDateBegin(): ?\DateTimeInterface
	{
		return $this->dateBegin;
	}

	public function setDateBegin(\DateTimeInterface $dateBegin): self
	{
		$this->dateBegin = $dateBegin;

		return $this;
	}

	public function getDateEnd(): ?\DateTimeInterface
	{
		return $this->dateEnd;
	}

	public function setDateEnd(\DateTimeInterface $dateEnd): self
	{
		$this->dateEnd = $dateEnd;

		return $this;
	}

	public function getPlace(): ?string
	{
		return $this->place;
	}

	public function setPlace(?string $place): self
	{
		$this->place = $place;

		return $this;
	}

	public function getIsActive(): ?bool
	{
		return $this->isActive;
	}

	public function setIsActive(bool $isActive): self
	{
		$this->isActive = $isActive;

		return $this;
	}

	public function getCategory(): ?CategoryNews
	{
		return $this->category;
	}

	public function setCategory(?CategoryNews $category): self
	{
		$this->category = $category;

		return $this;
	}

	/**
	 * @return Collection<int, User>
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

	public function getContent(): ?string
	{
		return $this->content;
	}

	public function setContent(string $content): self
	{
		$this->content = $content;

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

	public function getSummary(): string
	{
		if ($this->resume != null && $this->resume != '') {
			return $this->resume;
		}
		return substr(strip_tags($this->content), 0, 254) . '…';
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
}
