<?php

namespace App\Controller\Main;

use App\Form\Main\ProfileEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     */
    public function index(): Response
    {
        return $this->render(
            'main/profile/index.html.twig',
            [
            ]
        );
    }

    /**
     * @param  Request  $request
     * @param  EntityManagerInterface  $entityManager
     * @Route("/profile/edit", name="app_profile_edit")
     *
     * @return Response
     */
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render(
            'main/profile/edit.html.twig',
            ['form' => $form->createView()]
        );
    }
}
