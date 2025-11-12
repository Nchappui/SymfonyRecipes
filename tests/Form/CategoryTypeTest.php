<?php

namespace App\Tests\Form;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryTypeTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testSubmitValidData(): void
    {
        // Arrange : Préparer les données du formulaire
        $formData = [
            'name' => 'Cuisine Française',
            'slug' => 'cuisine-francaise',
            'country' => 'FR',
        ];

        $category = new Category();
        $form = static::getContainer()->get('form.factory')->create(CategoryType::class, $category);
        $form->submit($formData);

        // Assert : Vérifications
        $this->assertTrue($form->isSynchronized(), 'Le formulaire devrait être synchronisé');

        // Valider manuellement l'entité
        $violations = $this->validator->validate($category);
        $this->assertCount(0, $violations, 'Il ne devrait pas y avoir de violations');

        $this->assertEquals('Cuisine Française', $category->getName());
        $this->assertEquals('cuisine-francaise', $category->getSlug());
        $this->assertEquals('FR', $category->getCountry());
    }

    public function testSubmitWithoutSlugGeneratesAutoSlug(): void
    {
        $formData = [
            'name' => 'Plats Italiens',
            'slug' => '',
            'country' => 'IT',
        ];

        $category = new Category();
        $form = static::getContainer()->get('form.factory')->create(CategoryType::class, $category);
        $form->submit($formData);

        $violations = $this->validator->validate($category);
        $this->assertCount(0, $violations);
        $this->assertEquals('plats-italiens', $category->getSlug());
    }

    public function testInvalidCountryCode(): void
    {
        $formData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'country' => 'INVALID',
        ];

        $category = new Category();
        $form = static::getContainer()->get('form.factory')->create(CategoryType::class, $category);
        $form->submit($formData);

        // Valider l'entité
        $violations = $this->validator->validate($category);

        $this->assertGreaterThan(0, $violations->count(), 'Il devrait y avoir des violations');

        // Vérifier qu'il y a bien une erreur sur le champ country
        $hasCountryError = false;
        foreach ($violations as $violation) {
            if ($violation->getPropertyPath() === 'country') {
                $hasCountryError = true;
                break;
            }
        }
        $this->assertTrue($hasCountryError, 'Le champ country devrait avoir des erreurs');
    }

    public function testEmptyCountryIsInvalid(): void
    {
        $formData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'country' => '',
        ];

        $category = new Category();
        $form = static::getContainer()->get('form.factory')->create(CategoryType::class, $category);
        $form->submit($formData);

        $violations = $this->validator->validate($category);

        $this->assertGreaterThan(0, $violations->count());

        // Vérifier qu'il y a une erreur NotBlank
        $hasNotBlankError = false;
        foreach ($violations as $violation) {
            if ($violation->getPropertyPath() === 'country') {
                $hasNotBlankError = true;
                break;
            }
        }
        $this->assertTrue($hasNotBlankError, 'Le champ country devrait avoir une erreur NotBlank');
    }

    public function testNameTooShortIsInvalid(): void
    {
        $formData = [
            'name' => 'Test', // 4 caractères
            'slug' => 'test',
            'country' => 'FR',
        ];

        $category = new Category();
        $form = static::getContainer()->get('form.factory')->create(CategoryType::class, $category);
        $form->submit($formData);

        $violations = $this->validator->validate($category);

        $this->assertGreaterThan(0, $violations->count());

        // Vérifier erreur sur le nom
        $hasNameError = false;
        foreach ($violations as $violation) {
            if ($violation->getPropertyPath() === 'name') {
                $hasNameError = true;
                break;
            }
        }
        $this->assertTrue($hasNameError, 'Le champ name devrait avoir une erreur');
    }

    public function testInvalidSlugFormat(): void
    {
        $formData = [
            'name' => 'Test Category',
            'slug' => 'Invalid Slug!',
            'country' => 'FR',
        ];

        $category = new Category();
        $form = static::getContainer()->get('form.factory')->create(CategoryType::class, $category);
        $form->submit($formData);

        $violations = $this->validator->validate($category);

        $this->assertGreaterThan(0, $violations->count());

        $hasSlugError = false;
        foreach ($violations as $violation) {
            if ($violation->getPropertyPath() === 'slug') {
                $hasSlugError = true;
                break;
            }
        }
        $this->assertTrue($hasSlugError, 'Le champ slug devrait avoir une erreur');
    }

    public function testCountryCodeIsNormalized(): void
    {
        $formData = [
            'name' => 'Swiss Category',
            'slug' => 'swiss-category',
            'country' => 'ch',
        ];

        $category = new Category();
        $form = static::getContainer()->get('form.factory')->create(CategoryType::class, $category);
        $form->submit($formData);

        $violations = $this->validator->validate($category);
        $this->assertCount(0, $violations, 'Le code pays minuscule devrait être normalisé et valide');
        $this->assertEquals('ch', $category->getCountry());
    }

    public function testCreatedAtAndUpdatedAtAreSetOnSubmit(): void
    {
        $formData = [
            'name' => 'Time Test',
            'slug' => 'time-test',
            'country' => 'FR',
        ];

        $category = new Category();
        $form = static::getContainer()->get('form.factory')->create(CategoryType::class, $category);
        $form->submit($formData);

        $this->assertNotNull($category->getCreatedAt());
        $this->assertNotNull($category->getUpdatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $category->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $category->getUpdatedAt());
    }

    public function testFormHasExpectedFields(): void
    {
        $form = static::getContainer()->get('form.factory')->create(CategoryType::class);

        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('slug'));
        $this->assertTrue($form->has('country'));
        $this->assertTrue($form->has('save'));
    }
}
