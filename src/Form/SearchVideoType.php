<?php
namespace App\Form;
use App\Classe\SearchNews;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchVideoType extends AbstractType
{
	//creation du formulaire
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
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
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'method'=> 'GET',
			//desactivation de le crsf protection symfony
			'crsf_protection'=>false,
		]);
	}
}
