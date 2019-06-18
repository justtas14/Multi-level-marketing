<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Service\ConfigurationManager;

class ConfigurationInsterterExtension extends AbstractExtension
{

    /**
     * @var $cm
     */
    private $cm;

    /**
     * @param ConfigurationManager $cm
     */

    public function __construct(ConfigurationManager $cm)
    {
        $this->cm = $cm;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getConfiguration', [$this, 'getConfiguration']),
        ];
    }

    public function getConfiguration()
    {
        return $this->cm->getConfiguration();
    }
}
