<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="member")
 * @UniqueEntity(fields={"email"}, message="Cet email est déjà utilisé")
 * @Vich\Uploadable
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=180, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(type="json")
	 */
	private $roles = [];

	/**
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $firstname;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $lastname;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $isValide = false;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $phone;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $place;

	/**
	 * @ORM\ManyToMany(targetEntity=Document::class, mappedBy="author")
	 */
	private $documents;

	/**
	 * @ORM\ManyToMany(targetEntity=News::class, mappedBy="authors")
	 */
	private $newstypes;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $isVerified = false;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $webSite;


	public function __construct()
	{
		$this->documents = new ArrayCollection();
		$this->newstypes = new ArrayCollection();
		$this->createdAt = new \DateTime();
		$this->events = new ArrayCollection();
		$this->videos = new ArrayCollection();


	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUserIdentifier(): string
	{
		return (string)$this->email;
	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array
	{
		$roles = $this->roles;
		// guarantee every user at least has ROLE_USER
		$roles[] = 'ROLE_USER';

		return array_unique($roles);
	}

	public function setRoles(array $roles): self
	{
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @see PasswordAuthenticatedUserInterface
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Returning a salt is only needed, if you are not using a modern
	 * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
	 *
	 * @see UserInterface
	 */
	public function getSalt(): ?string
	{
		return null;
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials()
	{
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
	}

	public function getFirstname(): ?string
	{
		return $this->firstname;
	}

	public function setFirstname(string $firstname): self
	{
		$this->firstname = $firstname;

		return $this;
	}

	public function getLastname(): ?string
	{
		return $this->lastname;
	}

	public function setLastname(string $lastname): self
	{
		$this->lastname = $lastname;

		return $this;
	}

	public function getIsValide(): ?bool
	{
		return $this->isValide;
	}

	public function setIsValide(?bool $isValide): self
	{
		$this->isValide = $isValide;

		return $this;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function setPhone(?string $phone): self
	{
		$this->phone = $phone;

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


	public function __toString()
	{
		return $this->firstname . ' ' . $this->lastname;
	}

	public function getFullname(): ?string
	{
		return $this->lastname . ' ' . $this->firstname;
	}

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
			$document->addAuthor($this);
		}

		return $this;
	}

	public function removeDocument(Document $document): self
	{
		if ($this->documents->removeElement($document)) {
			$document->removeAuthor($this);
		}

		return $this;
	}

	/**
	 * @return Collection|News[]
	 */
	public function getNewstypes(): Collection
	{
		return $this->newstypes;
	}

	public function addNewstype(News $newstype): self
	{
		if (!$this->newstypes->contains($newstype)) {
			$this->newstypes[] = $newstype;
			$newstype->addAuthor($this);
		}

		return $this;
	}

	public function removeNewstype(News $newstype): self
	{
		if ($this->newstypes->removeElement($newstype)) {
			$newstype->removeAuthor($this);
		}

		return $this;
	}

	public function isVerified(): bool
	{
		return $this->isVerified;
	}

	public function setIsVerified(bool $isVerified): self
	{
		$this->isVerified = $isVerified;

		return $this;
	}

	public function getWebSite(): ?string
	{
		return $this->webSite;
	}

	public function setWebSite(?string $webSite): self
	{
		$this->webSite = $webSite;

		return $this;
	}

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $createdAt;

	/**
	 * @ORM\ManyToMany(targetEntity=Event::class, mappedBy="author")
	 */
	private $events;

	/**
	 * @ORM\ManyToMany(targetEntity=Video::class, mappedBy="authors")
	 */
	private $videos;

	/**
	 * @ORM\ManyToOne(targetEntity=Partner::class, inversedBy="users")
	 */
	private $partner;

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
	 * @return Collection<int, Event>
	 */
	public function getEvents(): Collection
	{
		return $this->events;
	}

	public function addEvent(Event $event): self
	{
		if (!$this->events->contains($event)) {
			$this->events[] = $event;
			$event->addAuthor($this);
		}

		return $this;
	}

	public function removeEvent(Event $event): self
	{
		if ($this->events->removeElement($event)) {
			$event->removeAuthor($this);
		}

		return $this;
	}


	public function getUsername(): ?string
	{
		return $this->getFullname();
	}

	/**
	 * @return Collection<int, Video>
	 */
	public function getVideos(): Collection
	{
		return $this->videos;
	}

	public function addVideo(Video $video): self
	{
		if (!$this->videos->contains($video)) {
			$this->videos[] = $video;
			$video->addAuthor($this);
		}

		return $this;
	}

	public function removeVideo(Video $video): self
	{
		if ($this->videos->removeElement($video)) {
			$video->removeAuthor($this);
		}

		return $this;
	}

	public function getPartner(): ?Partner
	{
		return $this->partner;
	}

	public function setPartner(?Partner $partner): self
	{
		$this->partner = $partner;

		return $this;
	}
}
