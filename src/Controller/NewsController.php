<?php

namespace App\Controller;



use App\Classe\SearchNews;

use App\Entity\CategoryDonnees;
use App\Entity\Document;
use App\Entity\News;
use App\Form\NewsType;

use App\Form\SearchNewsType;
use App\Form\SearchDocumentType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class NewsController extends AbstractController
{
    /**
     * @Route("/news", name="news_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
		$form = $this->createForm(SearchNewsType::class, null, ['action' => $this->generateUrl('news_index') ,'method' => 'GET']);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$keyword = $form->get('keyword')->getData();
			$from = $form->get('fromDate')->getData();
			$to = $form->get('toDate')->getData();
			$news = $this->getDoctrine()->getRepository(News::class)
				->findWithSearch($keyword, $from, $to);
		}
		else {
			$news = $this->getDoctrine()->getRepository(News::class)
				->findBy(['isActive' => true], ['createdAt' => 'DESC']);
		}

		$news = $paginator->paginate($news, $request->query->get('page', 1), 6);

		return $this->render('news/index.html.twig', [
			'news' => $news,
			'form' => $form->createView()
		]);
    }

    /**
     * @Route("/new/news", name="news_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
		$this->denyAccessUnlessGranted('ROLE_USER');

		$entityManager = $this->getDoctrine()->getManager();
		$news = new News();
		$form = $this->createForm(NewsType::class, $news);
		$form->handleRequest($request);
		$slugger = new AsciiSlugger();

		if ($form->isSubmitted() && $form->isValid()) {
			$news->setSlug($slugger->slug($news->getTitle()));
			$news->addAuthor($this->getUser());
            $entityManager->persist($news);
            $entityManager->flush();

			$this->addFlash('success', 'Votre document a été enresitré et est en attente de validation par un administrateur');
			return $this->redirectToRoute('account');
        }

        return $this->render('news/new.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/news/{slug}", name="news_show", methods={"GET"})
     */
    public function show(News $news): Response
    {
        return $this->render('show/indexNews.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/edit/news/{slug}", name="news_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, News $news): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
			$this->addFlash('success', 'Nouvelle modifié avec succès');

            return $this->redirectToRoute('account');
        }

        return $this->render('news/edit.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/news/{id}", name="news_delete", methods={"POST"})
     */
    public function delete(Request $request, News $news): Response
    {
        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($news);
            $entityManager->flush();
        }

        return $this->redirectToRoute('account');
    }



}
