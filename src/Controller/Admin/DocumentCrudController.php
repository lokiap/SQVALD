<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DocumentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Document::class;
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
			AssociationField::new('categorydonnees')
				->setRequired(true)
				->setLabel("Catégorie"),
			TextareaField::new('resume')
				->setRequired(false)
				->hideOnIndex()
				->setLabel("Résumé"),
			TextEditorField::new('content')
				->setRequired(true)
				->hideOnIndex()
				->setLabel("Contenu"),
			AssociationField::new('author')
				->setRequired(true)
				->setLabel("Auteur"),
			BooleanField::new('isActive')
				->setLabel("Activé ?"),
            ImageField::new('picture')
				->setRequired(true)
              ->setBasePath('uploads/')
              ->setUploadDir('public/uploads')
              ->setUploadedFileNamePattern('[randomhash].[extension]')
              ->setRequired(false)
				->setLabel("Image"),
            ImageField::new('brochureFilename')
				->setRequired(true)
                ->setBasePath('brochures/')
                ->setUploadDir('public/uploads/brochures')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
				->hideOnIndex()
				->setLabel("Fichier"),
			SlugField::new("slug")
				->setRequired(true)
				->setTargetFieldName("title")
				->hideOnIndex()
        ];
    }

}
