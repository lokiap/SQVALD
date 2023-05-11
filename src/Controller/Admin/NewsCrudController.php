<?php

namespace App\Controller\Admin;

use App\Entity\News;
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

class NewsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return News::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
			IdField::new('id')->hideOnForm(),
			TextField::new('title')
				->setRequired(true)
				->setLabel("Titre"),
			DateField::new('createdAt')
				->onlyOnIndex()
				->setLabel('Date de création'),
			TextareaField::new('resume')
				->setRequired(false)
				->hideOnIndex()
				->setLabel("Résumé"),
			TextEditorField::new('content')
				->setRequired(true)
				->hideOnIndex()
				->setLabel("Contenu"),
			AssociationField::new('authors')
				->setRequired(true)
				->setLabel("Auteur"),
			BooleanField::new('isActive')
				->setLabel("Activé ?"),
//			SlugField::new("slug")
//				->setRequired(true)
//				->setTargetFieldName("title")
//				->hideOnIndex()
        ];
    }
}
