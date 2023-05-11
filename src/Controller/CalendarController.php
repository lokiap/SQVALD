<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\CalendarType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/calendar")
 */
class CalendarController extends AbstractController {

	/**
	 * @Route("/", name="calendar")
	 */
	public function index(Request $request, EntityManagerInterface $entityManager): Response {

		$form = $this->createForm(CalendarType::class);
		$year = date('Y');
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$year = $form->get('year')->getData();
		}

		return $this->render('calendar/index.html.twig', [
			'form' => $form->createView(),
			'events' => $entityManager->getRepository(Event::class)->findByYear($year)
		]);
	}
}