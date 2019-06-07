<?php


namespace App\Form;

use App\Entity\Associate;
use PlumTreeSystems\FileBundle\Form\Type\PTSFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class)
            ->add('country', TextType::class)
            ->add('address', TextType::class)
            ->add('city', TextType::class)
            ->add('postcode', TextType::class)
            ->add('mobilePhone', TextType::class)
            ->add('homePhone', TextType::class, [
                'required' => false
            ])
            ->add('agreedToEmailUpdates', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'filled-in'
                ]
            ])
            ->add('agreedToTextMessageUpdates', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'filled-in'
                ]
            ])
            ->add('agreedToSocialMediaUpdates', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'filled-in'
                ]
            ])
            ->add('profilePicture', PTSFileType::class, ['multiple' => false, 'required' => false])
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'waves-effect waves-light btn'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Associate::class,
        ]);
    }
}
