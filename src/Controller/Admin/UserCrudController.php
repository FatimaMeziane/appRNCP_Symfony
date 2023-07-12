<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function configureCrud(crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Utilisateurs')
            ->setEntityLabelInSingular('Utilisateur')
            ->setPageTitle("index", 'Recette - Administration des utilisateurs')
            ->setPaginatorPageSize(10);
    }
    // Les champs de nos utilisateurs qu'on veut afficher et ajouter sur le dashbord
    public function configureFields(string $pageName): iterable
    {

        return [
            IdField::new('id')
                ->hideOnForm(),      
            TextField::new('fullName'),
            TextField::new('pseudo'),
            TextField::new('email'),
                // ->setFormTypeOption('disabled', 'disabled'),
            ArrayField::new('roles'),
                // ->hideOnIndex(),
            DateTimeField::new('createAt')
                ->setFormTypeOption('disabled', 'disabled'),
        ];
    }
}