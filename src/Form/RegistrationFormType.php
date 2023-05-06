<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{Email, IsTrue, Length, NotBlank};


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'label'=>'User email ',
                'required' =>true,
                'attr'=>[
                    'class'=>'form-control',
                    'autofocus'=>'autofocus',
                    'placeholder' => 'Please enter your email'
                ],
                'constraints'=> [
                    new NotBlank(['message' => 'Email must be not empty']),
                    new Email(['message' => 'Email not valid'])
                ],


            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'=>'I agree to thee <a href="#">privacy policy</a>',
                'required' =>true,
                'label_html'=>true,
                'mapped' => false,
                'attr'=>[
                    'class'=>'custom-control-input',
                ],
                'label_attr'=>[
                    'class'=>'custom-control-label'
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label'=>'User password',
                'required' =>true,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class'=>'form-control',
                    'placeholder' => 'Please enter your password'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
