<?php

namespace App\Form;

use App\Entity\CategoryNews;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchEventType extends AbstractType
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$categories = [];
		foreach ($this->entityManager->getRepository(CategoryNews::class)->findAll() as $category) {
			$categories[$category->getName()] = $category;
		}

		$builder
			->add('categories', ChoiceType::class, [
				'choices' => $categories,
				'label' => 'Catégories',
				'multiple' => true,
				'expanded' => true,
				'required' => false,
				'mapped' => false
			])
			->add('keyword', TextType::class, [
				'label' => 'Mot clé',
				'required' => false,
				'attr' => [
					'placeholder' => 'Rechercher par mot clé',
					'class' => 'form-control'
				],
				'mapped' => false
			])
			->add('place', TextType::class, [
				'label' => 'Lieu',
				'required' => false,
				'attr' => [
					'placeholder' => 'Rechercher par lieu',
					'class' => 'form-control'
				],
				'mapped' => false
			])
			->add('isEndBefore', ChoiceType::class, [
				'choices' => [
					'Début après le' => 2,
					'Fin avant le' => 1,
				],
				'data' => 2,
				'label' => false,
				'placeholder' => false,
				'multiple' => false,
				'expanded' => true,
				'required' => false,
				'mapped' => false
			])
			->add('date', DateType::class, [
				'label' => 'Date',
				'required' => false,
				'widget' => 'single_text',
				'format' => 'yyyy-MM-dd',
				'mapped' => false
			])
			->add('submit', SubmitType::class, ['label' => 'Rechercher']);
	}
}