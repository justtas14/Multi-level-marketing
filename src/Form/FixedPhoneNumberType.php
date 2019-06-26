<?php


namespace App\Form;

use App\Form\DataTransformer\FixedPhoneNumberToArrayTransformer;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Form\DataTransformer\PhoneNumberToArrayTransformer;
use Misd\PhoneNumberBundle\Form\DataTransformer\PhoneNumberToStringTransformer;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;

class FixedPhoneNumberType extends PhoneNumberType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (self::WIDGET_COUNTRY_CHOICE === $options['widget']) {
            $util = PhoneNumberUtil::getInstance();

            $countries = [];

            if (is_array($options['country_choices'])) {
                foreach ($options['country_choices'] as $country) {
                    $code = $util->getCountryCodeForRegion($country);

                    if ($code) {
                        $countries[$country] = $code;
                    }
                }
            }

            if (empty($countries)) {
                foreach ($util->getSupportedRegions() as $country) {
                    $countries[$country] = $util->getCountryCodeForRegion($country);
                }
            }

            $countryChoices = [];

            foreach (Intl::getRegionBundle()->getCountryNames() as $region => $name) {
                if (false === isset($countries[$region])) {
                    continue;
                }

                $countryChoices[sprintf('%s (+%s)', $name, $countries[$region])] = $region;
            }

            $transformerChoices = array_values($countryChoices);

            $countryOptions = $numberOptions = [
                'error_bubbling' => true,
                'required' => $options['required'],
                'disabled' => $options['disabled'],
                'translation_domain' => $options['translation_domain'],
            ];

            if (method_exists('Symfony\\Component\\Form\\AbstractType', 'getBlockPrefix')) {
                $choiceType = 'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType';
                $textType = 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType';
                $countryOptions['choice_translation_domain'] = false;

                // To be removed when dependency on Symfony Form is bumped to 3.1.
                if (!in_array(
                    'Symfony\\Component\\Form\\DataTransformerInterface',
                    class_implements('Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType')
                )
                ) {
                    $countryOptions['choices_as_values'] = true;
                }
            } else {
                // To be removed when dependency on Symfony Form is bumped to 2.7.
                $choiceType = 'choice';
                $textType = 'text';
                $countryChoices = array_flip($countryChoices);
            }

            $countryOptions['required'] = true;
            $countryOptions['choices'] = $countryChoices;
            $countryOptions['preferred_choices'] = $options['preferred_country_choices'];

            if ($options['country_placeholder']) {
                $countryOptions['placeholder'] = $options['country_placeholder'];
            }

            $builder
                ->add('country', $choiceType, $countryOptions)
                ->add('number', $textType, $numberOptions)
                ->addViewTransformer(new FixedPhoneNumberToArrayTransformer($transformerChoices));
        } else {
            $builder->addViewTransformer(
                new PhoneNumberToStringTransformer($options['default_region'], $options['format'])
            );
        }
    }
}
