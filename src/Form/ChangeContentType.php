<?php


namespace App\Form;

use PlumTreeSystems\FileBundle\Form\Type\PTSFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mainLogo', PTSFileType::class, ['multiple' => false, 'required' => false])
            ->add('termsOfServices', PTSFileType::class, ['multiple' => false, 'required' => false])
            ->add('hiddenMainLogoFile', HiddenType::class, ['mapped' => false, 'required' => false])
            ->add('hiddenTermsOfServiceFile', HiddenType::class, ['mapped' => false, 'required' => false])
            ->add('tosDisclaimer', TextareaType::class, [
                'required' => false,
                'label' => 'Terms of Services disclaimer content'
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Change content',
                'attr' => [
                    'class' => 'waves-effect waves-light btn'
                ]
            ]);
    }
}
