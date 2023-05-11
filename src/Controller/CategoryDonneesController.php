<?php

namespace App\Controller;

use App\Entity\CategoryDonnees;
use App\Form\CategoryDonneesType;
use App\Repository\CategoryDonneesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category/donnees")
 */
class CategoryDonneesController extends AbstractController
{
    /**
     * @Route("/", name="category_donnees_index", methods={"GET"})
     */
    public function index(CategoryDonneesRepository $categoryDonneesRepository): Response
    {
        return $this->render('category_donnees/index.html.twig', [
            'category_donnees' => $categoryDonneesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="category_donnees_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $categoryDonnee = new CategoryDonnees();
        $form = $this->createForm(CategoryDonneesType::class, $categoryDonnee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categoryDonnee);
            $entityManager->flush();

            return $this->redirectToRoute('category_donnees_index');
        }

        return $this->render('category_donnees/new.html.twig', [
            'category_donnee' => $categoryDonnee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_donnees_show", methods={"GET"})
     */
    public function show(CategoryDonnees $categoryDonnee): Response
    {
        return $this->render('category_donnees/show.html.twig', [
            'category_donnee' => $categoryDonnee,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_donnees_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CategoryDonnees $categoryDonnee): Response
    {
        $form = $this->createForm(CategoryDonneesType::class, $categoryDonnee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_donnees_index');
        }

        return $this->render('category_donnees/edit.html.twig', [
            'category_donnee' => $categoryDonnee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_donnees_delete", methods={"POST"})
     */
    public function delete(Request $request, CategoryDonnees $categoryDonnee): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categoryDonnee->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categoryDonnee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_donnees_index');
    }
}
