<?php


namespace App\Form;

use App\Entity\Associate;
use PlumTreeSystems\FileBundle\Form\Type\PTSFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'required' => true,
            ])
            ->add('country', CountryType::class, [
                'required' => true,
                'preferred_choices' => ['US', 'GB'],
            ])
            ->add('address', TextType::class, [
                'required' => true,
                'label' => 'Address Line 1'
            ])
            ->add('address2', TextType::class, [
                'required' => false,
                'label' => 'Address Line 2'
            ])
            ->add('city', TextType::class, [
                'required' => true,
            ])
            ->add('postcode', TextType::class, [
                'required' => true,
            ])
            ->add('mobilePhone', TelType::class, [
                'required' => true,
            ])
            ->add('homePhone', TelType::class, [
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
            ->add('agreedToTermsOfService', CheckboxType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'filled-in'
                ]
            ])
            ->add('dateOfBirth', DateType::class, [
                'required' => false,
                'years' => range(date('Y')-100, date('Y')),
                'widget' => 'choice',
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ],
                'format' => 'dd MMMM yyyy',
            ])
            ->add('profilePicture', PTSFileType::class, [
                'multiple' => false,
                'required' => false
            ])
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
            'cascade_validation' => true
        ]);
    }
}
