<?php

namespace App\Controller\Admin;

use App\Entity\Flag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator){}

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(FlagCrudController::class)
        ->generateUrl();
        return $this->redirect($url);
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Ctf Panel');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour à l\'accueil', 'fa fa-home', 'app_register_adm');
        yield MenuItem::linkToCrud('Flag', 'fa-solid fa-flag', Flag::class);

    }
}
