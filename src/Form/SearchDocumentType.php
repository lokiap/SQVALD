<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\CategoryDonnees;

use App\Entity\Document;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchDocumentType extends AbstractType
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$categories = [];
		foreach ($this->entityManager->getRepository(CategoryDonnees::class)->findAll() as $category) {
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
			->add('fromDate', DateType::class, [
				'label' => 'Du',
				'required' => false,
				'widget' => 'single_text',
				'format' => 'yyyy-MM-dd',
				'mapped' => false
			])
			->add('toDate', DateType::class, [
				'label' => 'Au',
				'required' => false,
				'widget' => 'single_text',
				'format' => 'yyyy-MM-dd',
				'mapped' => false
			])
			->add('submit', SubmitType::class, ['label' => 'Rechercher']);
	}
}
