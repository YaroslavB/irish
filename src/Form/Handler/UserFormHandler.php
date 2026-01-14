<?php

namespace App\Form\Handler;

use App\Entity\User;
use App\Utils\Manager\UserManager;
use Symfony\Component\Form\Form;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFormHandler
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @param UserManager                 $userManager
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(UserManager $userManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userManager = $userManager;
        $this->passwordHasher = $passwordHasher;
    }


    /**
     * @param Form $form
     *
     * @return User
     */
    public function processEditForm(Form $form): User
    {
        /** @var User $user */
        $user = $form->getData();
        if($form->has('newEmail') && !$user->getId()) {
            $user->setEmail($form->get('newEmail')->getData());
        }
        if($form->has('planePassword')){
            $plainPassword = $form->get('planePassword')->getData();
            $user->setPassword($this->passwordHasher->hashPassword($user,$plainPassword));
        }
        $this->userManager->save($user);
        return $user;
    }
}
