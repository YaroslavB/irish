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
        // Empty password on edit means "keep current"; the form type makes it required for new users
        $plainPassword = $form->has('plainPassword') ? $form->get('plainPassword')->getData() : null;
        if (is_string($plainPassword) && $plainPassword !== '') {
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        }
        $this->userManager->save($user);

        return $user;
    }
}
