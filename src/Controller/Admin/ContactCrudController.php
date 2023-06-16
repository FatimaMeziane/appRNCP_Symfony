<?php

namespace App\Controller\Admin;

use App\Entity\Contact;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Demande de contacts')
            ->setEntityLabelInSingular('Demandes de contact')
            ->setPageTitle("index", 'Recette - Administration des demandes de contact')
            ->setPaginatorPageSize(10);
    }
    
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnIndex(),
            TextField::new('fullName'),
            TextField::new('email')
                ->setFormTypeOption('disabled', 'disabled'),
            TextareaField::new('subject'),
            TextareaField::new('message')
            ->hideOnIndex(),
            DateTimeField::new('createdAt')
                ->setFormTypeOption('disabled', 'disabled'),
        ];
    }
    
}