<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Sessions
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=128)
     */
    private $sess_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $sess_lifetime;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $sess_time;

    /**
     * @ORM\Column(type="blob")
     */
    private $sess_data;
}
