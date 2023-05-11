<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\CategoryDonnees;
use App\Entity\CategoryNews;
use App\Entity\Document;


use App\Entity\DonneesType;
use App\Entity\News;
use App\Form\DocumentsType;

use App\Form\SearchDocumentType;
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

class DocumentController extends AbstractController
{

	/**
	 * @Route("/documents", name="documents_index", methods={"GET"})
	 */
	public function index(Request $request, PaginatorInterface $paginator): Response
	{
		$form = $this->createForm(SearchDocumentType::class, null, ['action' => $this->generateUrl('documents_index') ,'method' => 'GET']);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$keyword = $form->get('keyword')->getData();
			$categoriesRaw = $form->get('categories')->getData();
			$from = $form->get('fromDate')->getData();
			$to = $form->get('toDate')->getData();
			$categories = [];
			foreach ($categoriesRaw as $categoryRaw) {
				$categories[] = $categoryRaw->getId();
			}
			$documents = $this->getDoctrine()->getRepository(Document::class)
				->findWithSearch($keyword, $categories, $from, $to);
		}
		else {
			$documents = $this->getDoctrine()->getRepository(Document::class)
				->findBy(['isActive' => true], ['createdAt' => 'DESC']);
		}

		$documents = $paginator->paginate($documents, $request->query->get('page', 1), 6);

		return $this->render('documenttype/index.html.twig', [
			'documents' => $documents,
			'categories' => $this->getDoctrine()->getRepository(CategoryDonnees::class)->findAll(),
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/new/document", name="documenttype_new", methods={"GET","POST"})
	 */
	public function new(Request $request): Response
	{
		$this->denyAccessUnlessGranted('ROLE_USER');

		$entityManager = $this->getDoctrine()->getManager();
		$document = new Document();
		$form = $this->createForm(DocumentsType::class, $document);
		$form->handleRequest($request);
		$slugger = new AsciiSlugger();

		if ($form->isSubmitted() && $form->isValid()) {
			$document->setSlug($slugger->slug($document->getTitle()));
			$document->addAuthor($this->getUser());
			$entityManager->persist($document);
			$entityManager->flush();

			$this->addFlash('success', 'Votre document a été enresitré et est en attente de validation par un administrateur');
			return $this->redirectToRoute('account');
		}

		return $this->render('documenttype/new.html.twig', [
			'document' => $document,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/edit/document/{slug}", name="documenttype_edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, Document $document): Response
	{

		if (!$document->getAuthor()->contains($this->getUser())) {
			$this->addFlash('error', 'Vous n\'avez pas la permision de modifier ce document');
			return $this->redirectToRoute('home');
		}

		$form = $this->createForm(DocumentsType::class, $document);
		$form->handleRequest($request);


		if ($form->isSubmitted() && $form->isValid()) {


			$this->getDoctrine()->getManager()->flush();
			$this->addFlash('success', 'Document modifié avec succès');

			return $this->redirectToRoute('account');
		}

		return $this->render('documenttype/edit.html.twig', [
			'document' => $document,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/delete/document/{id}", name="documenttype_delete", methods={"POST"})
	 */
	public function delete(Request $request, Document $document): Response
	{
		if (!$document->getAuthor()->contains($this->getUser())) {
			$this->addFlash('error', 'Vous n\'avez pas la permision de supprimer ce document');
			return $this->redirectToRoute('home');
		}
		elseif ($this->isCsrfTokenValid('delete' . $document->getId(), $request->request->get('_token'))) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($document);
			$entityManager->flush();
		}
		$this->addFlash('success', 'Le document a été supprimé avec succès');
		return $this->redirectToRoute('account');
	}

	/**
	 * @Route("/document/{slug}", name="showDocument")
	 */
	public function show(Document $document): Response
	{

		if (!$document->getIsActive()) {
			$this->addFlash('error', 'Le document demandé n\'est pas disponible pour le moment');
			return $this->redirectToRoute('home');
		}
		return $this->render('show/indexDocument.html.twig', [
			'document' => $document,

		]);
	}
}
