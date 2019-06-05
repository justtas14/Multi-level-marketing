<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Service\ConfigurationManager;

class LogoInserterExtension extends AbstractExtension
{

    /**
     * @var $cm
     */
    private $cm;

    /**
     * @param ConfigurationManager
     */

    public function __construct(ConfigurationManager $cm)
    {
        $this->cm = $cm;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getLogo', [$this, 'getLogo']),
        ];
    }

    public function getLogo()
    {
        return $this->cm->getConfiguration()->getMainLogo();
    }
}
