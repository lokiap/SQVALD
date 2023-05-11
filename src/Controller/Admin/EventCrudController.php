<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventCrudController extends AbstractCrudController
{

	public static function getEntityFqcn(): string
	{
		return Event::class;
	}

	public function configureFields(string $pageName): iterable
	{
		return [
			IdField::new('id')->hideOnForm(),
			TextField::new('title')
				->setRequired(true)
				->setLabel("Titre"),
			AssociationField::new('category')
				->setRequired(true)
				->setLabel("Catégorie"),
			DateField::new('dateBegin')
				->setLabel('Date de début'),
			DateField::new('dateEnd')
				->setLabel('Date de fin'),
			TextField::new('place')
				->setLabel('Lieu')
				->setRequired(true),
			TextareaField::new('resume')
				->setRequired(false)
				->hideOnIndex()
				->setLabel("Résumé"),
			TextEditorField::new('content')
				->setRequired(true)
				->hideOnIndex()
				->setLabel("Contenu"),
			BooleanField::new('isActive')
				->setLabel("Activé ?"),
//			SlugField::new("slug")
//				->setRequired(true)
//				->setTargetFieldName("title")
//				->hideOnIndex()
		];
	}
}