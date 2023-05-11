<?php

namespace App\Controller;

use App\Entity\DonneesType;
use App\Form\DonneesTypeType;
use App\Repository\DonneesTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/donnees/type")
 */
class DonneesTypeController extends AbstractController
{
    /**
     * @Route("/", name="donnees_type_index", methods={"GET"})
     */
    public function index(DonneesTypeRepository $donneesTypeRepository): Response
    {
        return $this->render('donnees_type/index.html.twig', [
            'donnees_types' => $donneesTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="donnees_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $donneesType = new DonneesType();
        $form = $this->createForm(DonneesTypeType::class, $donneesType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($donneesType);
            $entityManager->flush();

            return $this->redirectToRoute('donnees_type_index');
        }

        return $this->render('donnees_type/new.html.twig', [
            'donnees_type' => $donneesType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="donnees_type_show", methods={"GET"})
     */
    public function show(DonneesType $donneesType): Response
    {
        return $this->render('donnees_type/show.html.twig', [
            'donnees_type' => $donneesType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="donnees_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DonneesType $donneesType): Response
    {
        $form = $this->createForm(DonneesTypeType::class, $donneesType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('donnees_type_index');
        }

        return $this->render('donnees_type/edit.html.twig', [
            'donnees_type' => $donneesType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="donnees_type_delete", methods={"POST"})
     */
    public function delete(Request $request, DonneesType $donneesType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$donneesType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($donneesType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('donnees_type_index');
    }
}
