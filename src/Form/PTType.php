<?php

namespace App\Form;

use FF\TextType;
use App\Entity\PropertyType;
use App\Form\FormFactoryListener;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type as FF;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PTType extends AbstractType
{

    function __construct(
        private readonly RequestStack $requestStack,
        private readonly FormFactoryListener $formFactoryListener
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $buttonLabel = $request->attributes->get('_route') === 'admin.types.create' ? 'Enrégistrer' : 'Éditer';

        $builder
            ->add('name', FF\TextType::class, [
                'label' => 'Nom',
                /* 'constraints' => [
                    new Assert\Length(min : 5),
                    new Assert\NotBlank()
                ] */
            ])
            ->add('slug', FF\TextType::class, [
                'label' => 'Slug',
                'required' => false,
                /* 'constraints' => [
                    new Assert\Length(min : 5),
                ] */
            ])
            /* ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text'
            ]) */
            ->add('save', FF\SubmitType::class, [
                'label' => $buttonLabel,
                'attr' => [
                    'class' => 'btn btn-primary btn-sm'
                ]
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formFactoryListener->autoSlug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formFactoryListener->attachTimestamps(PropertyType::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropertyType::class,
            'validation_groups' => ['Default']
        ]);
    }
}
