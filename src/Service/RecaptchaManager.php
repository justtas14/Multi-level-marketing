<?php


namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RecaptchaManager
{
    /** @var ParameterBagInterface $parameterBag */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function validateRecaptcha($recaptchaResponse, $secretKey)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .
            '&response=' . urlencode($recaptchaResponse);
        $response = file_get_contents($url);
        $responseKeys = json_decode($response, true);

        $env = $this->parameterBag->get('kernel.environment');

        if (!$recaptchaResponse && $env !== 'test') {
//            return 'Please check the captcha form';
        } elseif (!$responseKeys["success"] && $env !== 'test') {
//            return 'You are the spammer!';
        }
        return '';
    }
}
