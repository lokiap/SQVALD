<?php

namespace App\Controller;

use App\Entity\Partner;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PartnerController extends AbstractController
{
	/**
	 * @Route("/partners", name="partners_index")
	 */
	public function index(): Response {
		return $this->render('partner/index.html.twig', [
			'partners' => $this->getDoctrine()->getRepository(Partner::class)->findAll()
		]);
	}
}