<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\StaticStorage\UserStaticStorage;
use App\Entity\User;
use App\Form\Admin\EditFormCategoryType;
use App\Form\Admin\EditUserFormType;
use App\Form\DTO\EditCategoryDto;
use App\Form\Handler\CategoryFormHandler;
use App\Form\Handler\UserFormHandler;
use App\Repository\UserRepository;
use App\Utils\Manager\CategoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/admin/user', name: 'admin_user_')]
class UserController extends AbstractController
{
    /**
     * Show all category
     */
    #[Route(path: '/list', name: 'list')]
    public function list(UserRepository $userRepository): Response
    {

        $users = $userRepository->findBy(
            ['isDeleted' => false],
            ['id' => 'DESC']
        );
        return $this->render(
            'admin/user/list.html.twig',
            ['users' => $users]
        );
    }

    /**
     * @param Request         $request
     * @param UserFormHandler $userFormHandler
     * @param User|null       $user
     *
     * @return Response
     */
    #[Route(path: '/edit/{id}', name: 'edit')]
    #[Route(path: '/add', name: 'add')]
    public function edit(
        Request $request,
        UserFormHandler $userFormHandler,
        User $user = null
    ): Response {

        if(!$user){
            $user = new User();
        }
        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userFormHandler->processEditForm($form);

            $this->addFlash('success', 'User  saved');
            return $this->redirectToRoute(
                'admin_user_edit',
                ['id' => $user->getId()]
            );
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'User not saved');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form'     => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Delete  product
     */
    #[Route(path: '/delete/{id}', name: 'delete')]
    public function delete(
        Category $category,
        CategoryManager $categoryManager
    ): Response
    {
        $categoryManager->remove($category);
        $this->addFlash('warning', 'Product delete');
        return $this->redirectToRoute('admin_category_list');
    }
}