<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Gregwar\CaptchaBundle\Type\CaptchaType;

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
            ->add('captcha', CaptchaType::class, [
                'width' => 200,
                'height' => 100,
                'length' => 6,
                'ignore_all_effects' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['label'],
                'attr' => [
                    'class' => 'waves-effect waves-light btn'
                ]
            ]);
    }
}
