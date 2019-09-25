<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'attr' => [
                    'class' => 'validate'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'validate'
                ]
            ])
            ->add('submit', SubmitType::class, [
                    'label' => 'Get Invited',
                'attr' => [
                    'class' => 'waves-effect waves-light btn'
                ]
            ]);
    }
}
