<?php

namespace App\Form;

use App\Entity\CategoryDonnees;
use App\Entity\CategoryNews;
use App\Entity\Document;
use App\Entity\Event;
use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EventType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('category', EntityType::class, [
				'class' => CategoryNews::class,
				'choice_label' => 'name',
				'placeholder' => 'Choisir une catégorie',
				'label' => 'Catégory'
			])
			->add('title', TextType::class, ['label' => 'Titre'])
			->add('place', TextType::class, ['required' => false, 'label' => 'Lieu'])
			->add('dateBegin', DateType::class, [
				'label' => 'Début',
				'widget' => 'single_text',
				'format' => 'yyyy-MM-dd'
			])
			->add('dateEnd', DateType::class, [
				'label' => 'Fin',
				'widget' => 'single_text',
				'format' => 'yyyy-MM-dd'
			])
			->add('resume', TextareaType::class, ['required' => false, 'label' => 'Résumé'])
			->add('content', CKEditorType::class, ['required' => false,'label' => 'Contenu'])
			->add('author', EntityType::class, [
				'required' => false,
				'label' => 'Autre(s) auteur(s)',
				'help' => 'Séléctionner les utilisateurs ayant participé à la réalisation de ce document, en plus de vous-même',
				'class' => User::class,
				'multiple' => true,
				'attr' => [
					'class' => 'js-author-multiple'
				]
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Event::class
		]);
	}

}