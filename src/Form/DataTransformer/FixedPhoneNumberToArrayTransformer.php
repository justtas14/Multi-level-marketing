<?php

namespace App\Form\DataTransformer;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Form\DataTransformer\PhoneNumberToArrayTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Phone number to array transformer.
 */
class FixedPhoneNumberToArrayTransformer extends PhoneNumberToArrayTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform($phoneNumber)
    {
        if (!$phoneNumber) {
            return parent::transform($phoneNumber);
        }
        $util = PhoneNumberUtil::getInstance();

        $number = $util->format($phoneNumber, PhoneNumberFormat::INTERNATIONAL);

        $result = parent::transform($phoneNumber);

        $country = $result['country'];

        return [
            'country' => $country,
            'number' => $number,
        ];
    }
}
