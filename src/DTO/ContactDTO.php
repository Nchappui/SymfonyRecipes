<?php

namespace App\DTO;

use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

enum Services: string
{
    case Commerciaux = 'commerce@example.com';
    case Support = 'support@example.com';
    case Marketing = 'marketing@example.com';
}
class ContactDTO
{
    #[Assert\NotBlank()]
    #[Assert\Length(max: 50)]
    private string $firstName = '';

    #[Assert\NotBlank()]
    #[Assert\Length(max: 50)]
    private string $lastName = '';

    #[Assert\NotBlank()]
    #[Assert\Email()]
    private string $email = '';

    #[Assert\NotBlank()]
    #[Assert\Length(min: 5)]
    private ?string $content = '';

    private ?Services $toService = Services::Commerciaux;

    public function getToService(): ?Services
    {
        return $this->toService;
    }

    public function getEmailOfService(): string
    {
        return $this->toService->value;
    }

    public function setToService(Services $toService): static
    {
        $this->toService = $toService;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }


    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
