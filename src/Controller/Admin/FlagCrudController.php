<?php

namespace App\Controller\Admin;

use App\Entity\Flag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class FlagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Flag::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            yield IdField::new('id','Identifiant')->hideOnForm(),
            yield TextField::new('label'),
            yield IntegerField::new('valeur'),
            yield TextareaField::new('description')->renderAsHtml(),
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des flags')
            ->setPageTitle('edit', 'Modifier un flag')
            ->setPageTitle('new', 'Ajouter un flag')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            //Nom des boutons page liste
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('fa-solid fa-plus')->setLabel('Ajouter');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setLabel('Modifier');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setLabel('Supprimer');
            })
            //nom des boutons page modifier
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Sauvegarder');
            })
            ->remove(Crud::PAGE_EDIT,Action::SAVE_AND_CONTINUE)
            //nom des boutons page ajout
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Ajouter');
            })
            ->remove(Crud::PAGE_NEW,Action::SAVE_AND_ADD_ANOTHER)
        ;
    }
}
