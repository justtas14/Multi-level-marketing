<?php


namespace App\Validator\Constraints;

use App\Entity\File;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsImageValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value) {
            if (!$value instanceof File) {
                throw new UnexpectedTypeException($value, File::class);
            }

            $uploadedFile = $value->getUploadedFileReference();
            if ($uploadedFile !== null &&
                (!in_array($uploadedFile->getClientMimeType(), $this->getAllowedImageTypes()))
            ) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }

    private function getAllowedImageTypes()
    {
        return [
            'image/png',
            'image/jpeg',
            'image/webp',
        ];
    }
}
