<?php

namespace App\Controller\Admin;


use App\Entity\CategoryDonnees;
use App\Entity\CategoryNews;
use App\Entity\Document;


use App\Entity\Event;
use App\Entity\Partner;
use App\Entity\News;
use App\Entity\Partners;
use App\Entity\User;

use App\Entity\Video;
use App\Repository\DocumentRepository;
use App\Repository\EventRepository;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
	private $documentRepository;
	private $eventRepository;
	private $newsRepository;
	private $userRepository;
	private $videoRepository;

	public function __construct(
		DocumentRepository $documentRepository,
		EventRepository    $eventRepository,
		NewsRepository     $newsRepository,
		UserRepository     $userRepository,
		VideoRepository    $videoRepository)
	{
		$this->documentRepository = $documentRepository;
		$this->eventRepository = $eventRepository;
		$this->newsRepository = $newsRepository;
		$this->userRepository = $userRepository;
		$this->videoRepository = $videoRepository;
	}

	/**
	 * @Route("/admin", name="admin")
	 */
	public function index(): Response
	{

		$users = $this->userRepository->findBy(['isVerified' => true, 'isValide' => false], ['createdAt' => 'DESC']);
		$documents = $this->documentRepository->findBy(['isActive' => false], ['createdAt' => 'DESC']);
		$events = $this->eventRepository->findBy(['isActive' => false], ['createdAt' => 'DESC']);
		$news = $this->newsRepository->findBy(['isActive' => false], ['createdAt' => 'DESC']);
		$videos = $this->videoRepository->findBy(['isActive' => false], ['createdAt' => 'DESC']);

		$data = array();
		if (!empty($users)) $data[] = $users;
		if (!empty($documents)) $data[] = $documents;
		if (!empty($events)) $data[] = $events;
		if (!empty($news)) $data[] = $news;
		if (!empty($videos)) $data[] = $videos;

		$lengths = array_map('count', $data);
		asort($lengths);
		$sortedData = array();
		foreach (array_keys($lengths) as $k) $sortedData[$k] = $data[$k];
		$sortedData = array_reverse($sortedData);

		$left = array();
		$right = array();
		$length = count($sortedData) - 1;

		for ($i = 0; $i <= $length; $i++) {
			$leftLength = 0;
			$rightLength = 0;
			foreach ($left as $object) $leftLength += count($object);
			foreach ($right as $object) $rightLength += count($object);
			if ($leftLength <= $rightLength) $left[] = $sortedData[$i];
			else $right[] = $sortedData[$i];
		}

		$splitedData = array($left, $right);


		//TODO 2022-05-20 : Modifier le twig pour afficher les entités en validations par taille

		return $this->render("admin/dashboard.html.twig", [
			'nbDocuments' => $this->documentRepository->count([]),
			'nbUsers' => $this->userRepository->count([]),
			'nbEvents' => $this->eventRepository->count([]),
			'nbNews' => $this->newsRepository->count([]),
			'nbVideos' => $this->videoRepository->count([]),
			'data' => $splitedData
		]);
	}

	public function configureDashboard(): Dashboard
	{
		return Dashboard::new()
			->setTitle('SQVALD');
	}

	public function configureMenuItems(): iterable
	{
		yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

		yield MenuItem::section('Documents');
		yield MenuItem::linkToCrud('Tous les documents', 'fas fa-file', Document::class);
		yield MenuItem::linkToCrud('Catégories', 'fas fa-list', CategoryDonnees::class);

		yield MenuItem::section('Évènements');
		yield MenuItem::linkToCrud('Tous les évènements', 'fas fa-calendar', Event::class);
		yield MenuItem::linkToCrud('Catégories', 'fas fa-list', CategoryNews::class);

		yield MenuItem::section('Nouvelles');
		yield MenuItem::linkToCrud('Toutes les nouvelles', 'fas fa-newspaper', News::class);

		yield MenuItem::section('Vidéos');
		yield MenuItem::linkToCrud('Toutes les vidéos', 'fas fa-video', Video::class);

		yield MenuItem::section();
		yield MenuItem::linkToCrud('Partenaires', 'fas fa-handshake', Partner::class);
		yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);

		yield MenuItem::section();
		yield MenuItem::linkToRoute('Sortie', 'fas fa-arrow-left', 'home');

	}
}
