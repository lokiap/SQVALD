<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ResendVerifyEmailForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		return $builder->add('submit', SubmitType::class, ['label' => 'Ré-envoyer']);
	}

}