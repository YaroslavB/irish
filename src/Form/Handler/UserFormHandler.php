<?php

namespace App\Form\Handler;

use App\Entity\User;
use App\Utils\Manager\UserManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFormHandler
{
    private UserManager $userManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserManager $userManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userManager = $userManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function processEditForm(FormInterface $form): User
    {
        /** @var User $user */
        $user = $form->getData();
        if ($form->has('newEmail') && $user->getId() === null) {
            $user->setEmail($form->get('newEmail')->getData());
        }
        if ($form->has('planePassword')) {
            $plainPassword = $form->get('planePassword')->getData();
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        }
        $this->userManager->save($user);

        return $user;
    }
}
