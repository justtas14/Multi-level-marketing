<?php


namespace App\Tests\Service;

use App\Validator\Constraints\IsImageValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;

class ImageValidatorTest extends TestCase
{
    /**
     *  Testing image Validator service when given in params unexpected file.
     */
    public function testImageValidator()
    {
        $constraint = $this->createMock(Constraint::class);

        $value = "unexpected";

        $isImageValidator = new IsImageValidator();

        $this->expectException(UnexpectedTypeException::class);

        $isImageValidator->validate($value, $constraint);
    }
}
