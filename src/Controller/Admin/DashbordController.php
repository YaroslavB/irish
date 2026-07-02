<?php

namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route(path: '/admin')]
class DashbordController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route(path: '/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/pages/dashboard.html.twig');
    }

}