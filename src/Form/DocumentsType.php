<?php

namespace App\Form;

use App\Entity\CategoryDonnees;
use App\Entity\Document;

use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class DocumentsType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('categorydonnees', EntityType::class, [
				'class' => CategoryDonnees::class,
				'choice_label' => 'name',
				'placeholder' => 'Choisir une catégorie',
				'label' => 'Catégorie'
			])
			->add('title', TextType::class, ['label' => 'Titre'])
			->add('resume', TextareaType::class, ['required' => false, 'label' => 'Résumé'])
			->add('content', CKEditorType::class, ['required' => false,'label' => 'Contenu'])
			->add('imageFile', VichImageType::class, [
				'delete_label' => 'Supprimer ?',
				'label' => 'Image',
				'required' => false,
				'allow_delete' => true,
				'download_uri' => false,
				'download_link' => false,
			])
			->add('brochureFile', VichFileType::class, [
				'delete_label' => 'Supprimer ?',
				'label' => 'Document',
				'required' => false,
				'allow_delete' => true,
				'download_uri' => false,
			])
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
			'data_class' => Document::class
		]);
	}


}
