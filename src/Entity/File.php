<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use PlumTreeSystems\FileBundle\Entity\File as PTSFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(name="prelaunch_file")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="name",
 *          column=@ORM\Column(
 *              type     = "string",
 *              length   = 191,
 *              unique   = true
 *          )
 *      )
 * ,
 *      @ORM\AttributeOverride(name="context",
 *          column=@ORM\Column(
 *              type     = "string",
 *              length   = 191
 *          )
 *      )
 * ,
 *      @ORM\AttributeOverride(name="originalName",
 *          column=@ORM\Column(
 *              type     = "string",
 *              length   = 191
 *          )
 *      )
 * })
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
