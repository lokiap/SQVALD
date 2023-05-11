<?php

namespace App\Controller\Admin;

use App\Entity\Partner;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PartnerCrudController extends AbstractCrudController
{
	public static function getEntityFqcn(): string
	{
		return Partner::class;
	}

	public function configureFields(string $pageName): iterable
	{
		return [
			IdField::new('id'),
			TextField::new('content', 'Nom')
				->setRequired(true),
			ImageField::new('illustration')
				->setBasePath('uploads/partners/')
				->setUploadDir('public/uploads/partners/')
				->setUploadedFileNamePattern('[randomhash].[extension]')
				->setRequired(false),
		];
	}

}
