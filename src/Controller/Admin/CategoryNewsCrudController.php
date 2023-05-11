<?php

namespace App\Controller\Admin;

use App\Entity\CategoryNews;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategoryNewsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryNews::class;
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
