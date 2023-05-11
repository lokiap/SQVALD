<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{

    /**
     * @Route("/", name="account")
     */
    public function index(Request $request, PartnerRepository $partnerRepository): Response
    {
		if ($request->getMethod() == Request::METHOD_POST) {
			$user = $this->getUser();
			if (!$user instanceof User) throw new UnsupportedUserException();

			$newFirstname = $request->request->get('firstname');
			$newLastname = $request->request->get('lastname');
			$newPhone = $request->request->get('phone');
			$newPlace = $request->request->get('place');
			$newWebSite = $request->request->get('webSite');
			$partner_id = $request->request->get('partner');
			$partner = $partnerRepository->find($partner_id);
			if ($user->getFirstname() != $newFirstname) $user->setFirstname($newFirstname);
			if ($user->getLastname() != $newLastname) $user->setLastname($newLastname);
			if ($user->getPhone() != $newPhone) {
				if (empty($newPhone)) $user->setPhone(null);
				else $user->setPhone($newPhone);
			}
			if ($user->getPlace() != $newPlace) {
				if (empty($newPlace)) $user->setPlace(null);
				else $user->setPlace($newPlace);
			}
			if ($user->getWebSite() != $newWebSite) {
				if (empty($newWebSite)) $user->setWebSite(null);
				else $user->setWebSite($newWebSite);
			}
			if ($user->getPartner() !== $partner) {
				if (empty($partner)) $user->setPartner(null);
				else $user->setPartner($partner);
			}

			$this->getDoctrine()->getManager()->flush();
		}

        return $this->render('account/index.html.twig', [
			'partners' => $partnerRepository->findAll()
		]);
    }
}
