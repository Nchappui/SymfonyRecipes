<?php

namespace App\Tests\Validator;

use App\Validator\TCSCountry;
use App\Validator\TCSCountryValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class TCSCountryValidatorTest extends ConstraintValidatorTestCase
{

    protected function createValidator(): TCSCountryValidator
    {
        return new TCSCountryValidator();
    }

    public function testNullIsAllowed(): void
    {
        $constraint = new TCSCountry();
        $this->validator->validate(null, $constraint);

        $this->assertNoViolation();
    }


    public function testEmptyIsAllowed(): void
    {
        $constraint = new TCSCountry();
        $this->validator->validate("", $constraint);

        $this->assertNoViolation();
    }

    public function testValidCountryCode(): void
    {
        $this->validator->validate('FR', new TCSCountry());
        $this->assertNoViolation();
    }


    public function testValidCountryCodeLowercase(): void
    {
        $this->validator->validate('fr', new TCSCountry());

        $this->assertNoViolation();
    }

    public function testValidCountryCodeWithSpaces(): void
    {
        $this->validator->validate('  fr  ', new TCSCountry());

        $this->assertNoViolation();
    }
    public function testInvalidCountryCode(): void
    {
        $constraint = new TCSCountry();

        $this->validator->validate('XX', $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ country }}', 'XX')
            ->assertRaised();
    }

    public function testInvalidCountryCodeFullName(): void
    {
        $constraint = new TCSCountry();

        $this->validator->validate('France', $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ country }}', 'France')
            ->assertRaised();
    }

    #[DataProvider('validCountryCodesProvider')]
    public function testMultipleValidCountries(string $country): void
    {
        $this->validator->validate($country, new TCSCountry());

        $this->assertNoViolation();
    }

    public static function validCountryCodesProvider(): array
    {
        return [
            ['FR'],
            ['CH'],
            ['DE'],
            ['IT'],
            ['BE'],
            ['US'],
            ['LI'], // Liechtenstein
            ['XZ'], // Kosovo
        ];
    }

    #[DataProvider('invalidCountryCodesProvider')]
    public function testMultipleInvalidCountries(string $country): void
    {
        $constraint = new TCSCountry();

        $this->validator->validate($country, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ country }}', $country)
            ->assertRaised();
    }

    public static function invalidCountryCodesProvider(): array
    {
        return [
            ['XX'],
            ['ZZ'],
            ['ABC'],
            ['123'],
            ['France'],
            ['Suisse'],
        ];
    }

    public function testInvalidCountryCodeHasCorrectMessage(): void
    {
        $constraint = new TCSCountry();
        $invalidCode = 'XX';

        $this->validator->validate($invalidCode, $constraint);

        // Vérifier que le bon message template est utilisé
        $this->buildViolation('This is not a valid country code')
            ->setParameter('{{ country }}', $invalidCode)
            ->assertRaised();
    }
}
