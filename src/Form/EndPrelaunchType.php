<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class EndPrelaunchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prelaunchEnded', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'filled-in'
                ]
            ])
            ->add('landingContent', TextareaType::class)
            ->add('Submit', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'waves-effect waves-light btn'
                ]
            ]);
    }
}
