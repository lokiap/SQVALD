<?php

namespace App\Form;

use App\Entity\CategoryNews;
use App\Entity\News;
use App\Entity\User;
use Doctrine\DBAL\Types\IntegerType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;


class NewsType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title', TextType::class, ['label' => 'Titre'])
			->add('resume', TextareaType::class, ['required' => false, 'label' => 'Résumé'])
			->add('content', CKEditorType::class, ['required' => false, 'label' => 'Contenu'])
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
			'data_class' => News::class,
		]);
	}
}
