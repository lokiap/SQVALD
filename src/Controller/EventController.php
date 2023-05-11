<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\CategoryDonnees;
use App\Entity\CategoryNews;
use App\Entity\Document;


use App\Entity\DonneesType;
use App\Entity\Event;
use App\Entity\News;
use App\Form\DocumentsType;

use App\Form\EventType;
use App\Form\SearchDocumentType;
use App\Form\SearchEventType;
use App\Repository\CategoryDonneesRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

class EventController extends AbstractController
{

	/**
	 * @Route("/events", name="events_index", methods={"GET"})
	 */
	public function index(Request $request, PaginatorInterface $paginator): Response
	{
		$form = $this->createForm(SearchEventType::class, null, ['action' => $this->generateUrl('events_index') ,'method' => 'GET']);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$keyword = $form->get('keyword')->getData();
			$place = $form->get('place')->getData();
			$categoriesRaw = $form->get('categories')->getData();
			$isEndBefore = $form->get('isEndBefore')->getData();
			$date = $form->get('date')->getData();
			$categories = [];
			foreach ($categoriesRaw as $categoryRaw) {
				$categories[] = $categoryRaw->getId();
			}
			$events = $this->getDoctrine()->getRepository(Event::class)
				->findWithSearch($keyword, $place, $categories, $isEndBefore, $date);
		}
		else {
			$events = $this->getDoctrine()->getRepository(Event::class)
				->findBy(['isActive' => true], ['createdAt' => 'DESC']);
		}

		$events = $paginator->paginate($events, $request->query->get('page', 1), 6);

		return $this->render('event/index.html.twig', [
			'events' => $events,
			'categories' => $this->getDoctrine()->getRepository(CategoryNews::class)->findAll(),
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/new/event", name="event_new", methods={"GET","POST"})
	 */
	public function new(Request $request): Response
	{
		$this->denyAccessUnlessGranted('ROLE_USER');

		$entityManager = $this->getDoctrine()->getManager();
		$event = new Event();
		$form = $this->createForm(EventType::class, $event);
		$form->handleRequest($request);
		$slugger = new AsciiSlugger();

		if ($form->isSubmitted() && $form->isValid()) {
			$event->setSlug($slugger->slug($event->getTitle()));
			$event->addAuthor($this->getUser());
			$entityManager->persist($event);
			$entityManager->flush();

			$this->addFlash('success', 'Votre évènement a été enresitré et est en attente de validation par un administrateur');
			return $this->redirectToRoute('account');
		}

		return $this->render('event/new.html.twig', [
			'event' => $event,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/edit/event/{slug}", name="event_edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, Event $event): Response
	{

		if (!$event->getAuthor()->contains($this->getUser())) {
			$this->addFlash('error', 'Vous n\'avez pas la permision de modifier cet évènement');
			return $this->redirectToRoute('home');
		}

		$form = $this->createForm(EventType::class, $event);
		$form->handleRequest($request);


		if ($form->isSubmitted() && $form->isValid()) {
			$this->getDoctrine()->getManager()->flush();
			$this->addFlash('success', 'Évènement modifié avec succès');
			return $this->redirectToRoute('account');
		}

		return $this->render('event/edit.html.twig', [
			'event' => $event,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/delete/event/{id}", name="event_delete", methods={"POST"})
	 */
	public function delete(Request $request, Event $event): Response
	{
		if (!$event->getAuthor()->contains($this->getUser())) {
			$this->addFlash('error', 'Vous n\'avez pas la permision de supprimer cet évènement');
			return $this->redirectToRoute('home');
		}
		elseif ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($event);
			$entityManager->flush();
		}
		$this->addFlash('success', 'L\'évènement a été supprimé avec succès');
		return $this->redirectToRoute('account');
	}

	/**
	 * @Route("/event/{slug}", name="showEvent")
	 */
	public function show(Event $event): Response
	{

		if (!$event->getIsActive()) {
			$this->addFlash('error', 'L\'évènement demandé n\'est pas disponible pour le moment');
			return $this->redirectToRoute('home');
		}
		return $this->render('show/indexEvent.html.twig', [
			'event' => $event,

		]);
	}
}
