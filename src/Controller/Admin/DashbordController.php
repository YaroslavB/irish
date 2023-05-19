<?php

namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 *
 * @Route("/admin")
 */
class DashbordController extends AbstractController
{

    /**
     * @Route("/dashboard", name="admin_dashboard")
     * @return Response
     */
    public function dashboard(): Response
    {
        return $this->render('admin/pages/dashboard.html.twig');
    }

}