<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'attr' => [
                    'class' => 'validate'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Full name cannot be empty']),
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'validate'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Email cannot be empty']),
                    new Email(['message' => 'Invalid email'])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'send',
                'attr' => [
                    'class' => 'waves-effect waves-light btn'
                ]
            ]);
    }
}
