<?php

namespace App\Form;

use App\Entity\Partner;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', Texttype::class, [
                'label'=>"Votre prénom",
                'constraints' => new length([
                    'min'=> 2,
                    'max'=> 30])
			])
            ->add('lastname',TextType::class, [
                'label' => "Votre nom",
                'constraints' => new length([
                    'min'=> 2,
                    'max'=> 30])
            ])
            ->add('email', EmailType::class, [
                'label' => "Votre email",
                'constraints' => new length([
                    'min'=> 2,
                    'max'=> 55])
            ])

            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
				'invalid_message' => 'Les mots de passes ne sont pas identique',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096
                    ]),
                ],
                'first_options' =>[
                    'label' => 'Mot de passe',

                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                ]
            ])
			->add('phone', TextType::class, [
				'label'=>'Tél',
				'required' => false
			])

			->add('place', TextType::class, [
				'label'=>'Localisation',
				'attr' => [
					'placeholder' => 'ex: Université de Tours'
				],
				'required' => false
			])

            ->add('webSite', TextType::class, [
                'label' => 'Site Web',
				'attr' => [
					'placeholder' => 'ex: example.com'
				],
				'required' => false
            ])

			->add('partner', EntityType::class, [
				'class' => Partner::class,
				'label' => 'Partenaire',
				'choice_label' => 'content'
			])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
