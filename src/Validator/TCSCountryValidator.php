<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class TCSCountryValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var TCSCountry $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // Normalise to uppercase
        $countryCode = strtoupper(trim($value));

        // Check if valid country code
        if (!in_array($countryCode, $constraint->countries, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ country }}', $value)
                ->addViolation();
        }
    }
}
