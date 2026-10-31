<?php

namespace App\Form\Admin;

use App\Entity\StaticStorage\UserStaticStorage;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $user = $builder->getData();
        $isNew = !$user instanceof User || $user->getId() === null;

        $builder
            ->add(
                'newEmail',
                EmailType::class,
                [
                    'label'       => 'Email',
                    'required'    => $isNew,
                    'mapped'      => false,
                    'attr'        => ['class' => 'form-control'],
                    'constraints' => $isNew ? [new NotBlank(), new Email()] : [],
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'label'       => $isNew ? 'Password' : 'New password (leave empty to keep current)',
                    'required'    => $isNew,
                    'mapped'      => false,
                    'attr'        => ['class' => 'form-control', 'autocomplete' => 'new-password'],
                    'constraints' => $isNew
                        ? [new NotBlank(), new Length(min: 8, max: 4096)]
                        : [new Length(min: 8, max: 4096)],
                ]
            )
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'required' => false,
                'choices' => array_flip(UserStaticStorage::getUserRoleChoices()),
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add(
                'fullName',
                TextType::class,
                [
                    'label'    => 'Full name',
                    'required' => false,
                    'attr'     => ['class' => 'form-control'],
                ]
            )
            ->add('phone', TextType::class, [
                'label'    => 'Enter phone',
                'required' => false,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('address', TextType::class, [
                'label'    => 'Enter address',
                'required' => false,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('zipcode', TextType::class, [
                'label'    => 'Enter zipcode',
                'required' => false,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('isDeleted' ,CheckboxType::class,
                [
                    'label'      => 'is Deleted',
                    'required'   => false,
                    'attr'       => ['class' => 'form-check-input'],
                    'label_attr' => [
                        'class' => 'form-check-label',
                    ],
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
