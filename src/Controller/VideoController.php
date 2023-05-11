<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Video;
use App\Form\NewsType;
use App\Form\SearchVideoType;
use App\Form\VideoType;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function Sodium\add;

class VideoController extends AbstractController
{
	/**
	 * @Route("/videos", name="videos_index", methods={"GET"})
	 */
	public function index(Request $request, PaginatorInterface $paginator): Response
	{
		$form = $this->createForm(SearchVideoType::class, null, ['action' => $this->generateUrl('videos_index'), 'method' => 'GET']);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$keyword = $form->get('keyword')->getData();
			$from = $form->get('fromDate')->getData();
			$to = $form->get('toDate')->getData();
			$videos = $this->getDoctrine()->getRepository(Video::class)
			->findWithSearch($keyword, $from, $to);
		}
		else {
			$videos = $this->getDoctrine()->getRepository(Video::class)
				->findBy(['isActive' => true], ['createdAt' => 'DESC']);
		}

		$videos = $paginator->paginate($videos, $request->query->get('page', 1), 1);

		return $this->render('video/index.html.twig', [
			'videos' => $videos,
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/new/video", name="video_new", methods={"GET","POST"})
	 */
	public function new(Request $request, ValidatorInterface $validator): Response
	{
		$this->denyAccessUnlessGranted('ROLE_USER');

		$entityManager = $this->getDoctrine()->getManager();
		$video = new Video();
		$form = $this->createForm(VideoType::class, $video);
		$form->handleRequest($request);
		$slugger = new AsciiSlugger();

		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				$video->setSlug($slugger->slug($video->getTitle()));
				$video->addAuthor($this->getUser());
				if ($video->getLink() != null) {
					if (preg_match("/youtube\.com\/watch\?v=.+/", $video->getLink()) === 1) {
						$link_component = parse_url($video->getLink());
						parse_str($link_component['query'], $params);
						$video_id = $params['v'];
						$video->setLink('https://www.youtube.com/embed/'.$video_id);
					}
				}
				$entityManager->persist($video);
				$entityManager->flush();

				$this->addFlash('success', 'Votre video a été enresitré et est en attente de validation par un administrateur');
				return $this->redirectToRoute('account');
			}
			else {
				return $this->render('video/new.html.twig', [
					'video' => $video,
					'form' => $form->createView(),
					'errorMessage' => 'Veuillez remplir au moins un des deux champs (lien Youtube ou fichier)',
				]);
			}
		}

		return $this->render('video/new.html.twig', [
			'video' => $video,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/video/{slug}", name="video_show", methods={"GET"})
	 */
	public function show(Video $video): Response
	{
		return $this->render('video/show.html.twig', [
			'video' => $video
		]);
	}

	/**
	 * @Route("/edit/video/{slug}", name="video_edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, Video $video): Response
	{
		$form = $this->createForm(VideoType::class, $video);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				if ($video->getLink() != null) {
					if (preg_match("/youtube\.com\/watch\?v=.+/", $video->getLink()) === 1) {
						$link_component = parse_url($video->getLink());
						parse_str($link_component['query'], $params);
						$video_id = $params['v'];
						$video->setLink('https://www.youtube.com/embed/'.$video_id);
					}
				}
				$this->getDoctrine()->getManager()->flush();
				$this->addFlash('success', 'Nouvelle modifié avec succès');

				return $this->redirectToRoute('account');
			}
			else {
				return $this->render('video/edit.html.twig', [
					'video' => $video,
					'form' => $form->createView(),
					'errorMessage' => 'Veuillez remplir au moins un des deux champs (lien ou fichier)',
				]);
			}
		}

		return $this->render('video/edit.html.twig', [
			'video' => $video,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/delete/video/{id}", name="video_delete", methods={"POST"})
	 */
	public function delete(Request $request, Video $video): Response
	{
		if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($video);
			$entityManager->flush();
		}

		return $this->redirectToRoute('account');
	}
}