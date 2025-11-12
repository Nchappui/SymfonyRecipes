<?php

namespace App\Form;

use App\DTO\ContactDTO;
use App\DTO\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\Translation\t;

class ContactDTOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(t('firstName'), TextType::class)
            ->add(t('lastName'), TextType::class)
            ->add(t('email'), EmailType::class)
            ->add(t('content'), TextareaType::class)
            ->add(t('toService'), EnumType::class, ['class' => Services::class])
            ->add(t('send'), SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactDTO::class
        ]);
    }
}
