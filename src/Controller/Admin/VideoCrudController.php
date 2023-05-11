<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;

class VideoCrudController extends AbstractCrudController
{

	public static function getEntityFqcn(): string
	{
		return Video::class;
	}

	public function configureFields(string $pageName): iterable
	{
		return [
			IdField::new('id')->hideOnForm(),
			TextField::new('title')->setRequired(true),
			TextField::new('type')->onlyOnIndex(),
			TextField::new('link')
				->hideOnIndex()
				->setRequired(false),
			ImageField::new('src')
				->hideOnIndex()
				->setBasePath('uploads/videos/')
				->setUploadDir('public/uploads/videos/')
				->setUploadedFileNamePattern('[randomhash].[extension]')
				->setRequired(false),
			BooleanField::new('isActive')
				->setLabel("Activé ?"),
		];
	}
}