<?php

namespace App\Form;

use App\Entity\CategoryNews;
use App\Entity\News;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\DBAL\Types\IntegerType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;


class VideoType extends AbstractType implements EventSubscriberInterface
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->addEventSubscriber($this)
			->add('title', TextType::class, ['label' => 'Titre'])
			->add('link', UrlType::class, ['label' => 'Lien Youtube', 'required' => false])
			->add('srcFile', VichFileType::class, ['label' => 'Fichier', 'required' => false, 'disabled' => true])
			->add('authors', EntityType::class, [
				'class' => User::class,
				'required' => false,
				'multiple' => true,
				'label' => 'Autre(s) auteur(s)',
				'help' => 'Séléctionner les utilisateurs ayant participé à la réalisation de ce document, en plus de vous-même',
				'attr' => [
					'class' => 'js-author-multiple'
				]
			]);

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Video::class,
		]);
	}

	public static function getSubscribedEvents()
	{
		return [
			FormEvents::SUBMIT => 'ensureOneFieldIsSubmitted'
		];
	}

	public function ensureOneFieldIsSubmitted(FormEvent $event) {
		$data = $event->getData();
		if (!$data instanceof Video) return;

		$src = $data->getSrcFile();
		$link = $data->getLink();
		if (empty($src) && empty($link)) {
			throw new TransformationFailedException('Au moins un des deux champs doit être rempli', 0, null, 'Au moins un des deux champs doit être rempli');
		}
	}
}
