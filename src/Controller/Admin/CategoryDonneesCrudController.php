<?php

namespace App\Controller\Admin;

use App\Entity\CategoryDonnees;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategoryDonneesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryDonnees::class;
    }

	public function configureCrud(Crud $crud): Crud
	{
		return $crud->setEntityLabelInSingular('Catégorie de documents')->setEntityLabelInPlural('Catégories de documents');
	}

	/*
	public function configureFields(string $pageName): iterable
	{
		return [
			IdField::new('id'),
			TextField::new('title'),
			TextEditorField::new('description'),
		];
	}
	*/
}
