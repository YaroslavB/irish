<?php

namespace App\Form;

use App\Form\DTO\EditCategoryDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditFormCategoryType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Title',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => EditCategoryDto::class,
            ]
        );
    }
}
