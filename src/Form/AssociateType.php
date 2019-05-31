<?php


namespace App\Form;

use App\Entity\Associate;
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
            ->add('country', CountryType::class)
            ->add('address', TextType::class)
            ->add('city', TextType::class)
            ->add('postcode', TextType::class)
            ->add('mobilePhone', TextType::class)
            ->add('homePhone', TextType::class, [
                'required' => false
            ])
            ->add('agreedToEmailUpdates', CheckboxType::class, [
                'required' => false
            ])
            ->add('agreedToTextMessageUpdates', CheckboxType::class, [
                'required' => false
            ])
            ->add('agreedToSocialMediaUpdates', CheckboxType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Associate::class,
        ]);
    }
}
