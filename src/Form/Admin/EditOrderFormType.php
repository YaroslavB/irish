<?php

namespace App\Form\Admin;

use App\Entity\Order;
use App\Entity\StaticStorage\OrderStaticStorage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditOrderFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('status', ChoiceType::class, [
                'label'    => 'Status',
                'required' => true,
                'choices'  => array_flip(
                    OrderStaticStorage::getOrderStatusList()
                ),
                'attr'     => ['class' => 'form-control'],
            ])
            ->add(
                'isDeleted',
                CheckboxType::class,
                [
                    'label'      => 'is Deleted',
                    'required'   => false,
                    'attr'       => ['class' => 'form-check-input'],
                    'label_attr' => [
                        'class' => 'form-check-label',
                    ],
                ]
            )
            ->add('owner', EntityType::class, [
                'class'        => User::class,
                'choice_label' => function (User $user) {
                    return sprintf(
                        '#%s %s',
                        $user->getId(),
                        $user->getEmail()
                    );
                },
                'label'        => 'User',
                'attr'         => ['class' => 'form-control'],
                'required'     => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
