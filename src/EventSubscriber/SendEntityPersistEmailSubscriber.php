<?php

namespace App\EventSubscriber;

use App\Entity\Document;
use App\Entity\Event;
use App\Entity\News;
use App\Repository\UserRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use function Sodium\add;

class SendEntityPersistEmailSubscriber implements EventSubscriber
{
	private $mailer;
	private $userRepository;
	private $flashBag;

	public function __construct(MailerInterface $mailer, UserRepository $userRepository, FlashBagInterface $flashBag)
	{
		$this->mailer = $mailer;
		$this->userRepository = $userRepository;
		$this->flashBag = $flashBag;
	}

	public function postPersist(LifecycleEventArgs $args) {
		$entity = $args->getObject();

		if (!($entity instanceof Document || $entity instanceof Event || $entity instanceof News)) {
			return;
		}

		if ($entity instanceof Document) {
			$entityType = 'Document';
			$title = $entity->getTitle();
			$category = $entity->getCategorydonnees();
			$authors = [];
			foreach ($entity->getAuthor() as $author) {
				$authors[] = $author->getFullname();
			}
		}
		elseif ($entity instanceof Event) {
			$entityType = 'Évènement';
			$title = $entity->getTitle();
			$category = $entity->getCategory();
			$authors = [];
			foreach ($entity->getAuthor() as $author) {
				$authors[] = $author->getFullname();
			}
		}
		elseif ($entity instanceof News) {
			$entityType = 'Nouvelle';
			$title = $entity->getTitle();
			$category = null;
			$authors = [];
			foreach ($entity->getAuthors() as $author) {
				$authors[] = $author->getFullname();
			}
		}
		else {
			$entityType = null;
			$title = null;
			$category = null;
			$authors = [];
		}

		$adminUsers = $this->userRepository->findAdmins();

		$email = (new TemplatedEmail())
			->from('sqvald@example.com')
			->subject('[SQVALD] Nouvel objet en attente de validation')
			->htmlTemplate('email/new_object.html.twig')
			->context([
				'type' => $entityType,
				'title' => $title,
				'category' => $category,
				'authors' => $authors
			])
		;

		try {
			foreach ($adminUsers as $adminUser) {
				$this->mailer->send($email->to($adminUser->getEmail()));
			}
		} catch (TransportExceptionInterface$exception) {
			$this->flashBag->add('warning', 'Le service de messagerie est indisponible pour le moment, veuillez contacter un administrateur en cas de durée de validation trop longue');
		}
	}

	public function getSubscribedEvents(): array
	{
		return [
			Events::postPersist
		];
	}
}