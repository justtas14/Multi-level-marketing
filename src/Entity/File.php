<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use PlumTreeSystems\FileBundle\Entity\File as PTSFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(name="prelaunch_file")
 */
class File extends PTSFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    public function getId(): ?int
    {
        return $this->id;
    }
}
