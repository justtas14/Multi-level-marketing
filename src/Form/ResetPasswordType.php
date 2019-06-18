<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'validate'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send',
                'attr' => [
                    'class' => 'waves-effect waves-light btn'
                ]
            ]);
    }
}
