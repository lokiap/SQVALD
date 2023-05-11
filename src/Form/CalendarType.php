<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CalendarType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$startDate = 2022;
		$rangeYear = 5;
		$builder->add('year', ChoiceType::class, [
			'label' => 'Année',
			'choices' => array_combine(range($startDate, date('Y')+$rangeYear), range($startDate, date('Y')+$rangeYear)),
			'data' => date('Y'),
			'attr' => ['onChange' => 'this.form.submit()']
		]);
	}
}