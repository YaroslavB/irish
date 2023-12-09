<?php

namespace App\Form;

use App\Form\DTO\EditFormDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditFormProductType extends AbstractType
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
                    'label'       => 'Title',
                    'required'    => true,
                    'attr'        => ['class' => 'form-control'],
                    'constraints' => [
                        new NotBlank(),
                    ],

                ]
            )
            ->add(
                'price',
                NumberType::class,
                [
                    'scale'    => 2,
                    'html5'    => true,
                    'required' => true,
                    'attr'     => [
                        'class' => 'form-control',
                        'min'   => 0,
                        'step'  => 0.01,
                    ],

                ]
            )
            ->add(
                'quantity',
                IntegerType::class,
                [
                    'label'    => 'Quantity',
                    'required' => true,

                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'label'    => 'Description',
                    'required' => true,
                    'attr'     => [
                        'class' => 'form-control',
                        'style' => 'overflow:hidden',
                    ],
                ]
            )
            ->add(
                'newImage',
                FileType::class,
                [
                    'label'    => 'Choose image',
                    'required' => false,
                    'attr'     => ['class' => 'form-control-file'],
                ]
            )
            ->add(
                'isPublished',
                CheckboxType::class,
                [
                    'label'      => 'is Published',
                    'required'   => false,
                    'attr'       => ['class' => 'form-check-input'],
                    'label_attr' => [
                        'class' => 'form-check-label',
                    ],
                ]
            )
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
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => EditFormDto::class,
            ]
        );
    }
}
