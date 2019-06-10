<?php


namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class IsImage
 * @package App\Validator\Constraints
 * @Annotation
 */
class IsImage extends Constraint
{
    public $message = 'Only images may be uploaded';
}
