<?php

namespace App\Form;

use App\DTO\ContactDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as FF;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', FF\TextType::class, [
                'label' => 'Nom*',
                /* 'constraints' => new Sequentially([
                    new Assert\NotBlank(),
                    new Assert\Length(min:8),
                ]) */
            ])
            ->add('lastname',  FF\TextType::class, [
                'label' => 'Prénoms(Facultatif)',
                'required' => false,
                /* 'constraints' => new Sequentially([
                    new Assert\NotBlank(),
                    new Assert\Length(min:8),
                ]) */
            ])
            ->add('email',  FF\EmailType::class, [
                'label' => 'Email*',
                /* 'constraints' => new Sequentially([
                    new Assert\NotBlank(),
                    new Assert\Length(min:8),
                    new Assert\Email()
                ]) */
            ])
            ->add('phone',  FF\TextType::class, [
                'label' => 'Téléphone*',
                /* 'constraints' => new Sequentially([
                    new Assert\NotBlank(),
                ]) */
            ])
            ->add('message',  FF\TextareaType::class, [
                'label' => 'Message*',
                /* 'constraints' => new Sequentially([
                    new Assert\NotBlank(),
                    new Assert\Length(min:10),
                ]) */
            ])
            ->add('save', FF\SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-primary btn-sm'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDTO::class,
            'validation_groups' => ['Default']
        ]);
    }
}
