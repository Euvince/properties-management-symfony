<?php

namespace App\Form;

use App\Entity\Heating;
use App\Entity\Property;
use App\Entity\Specificity;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use App\Entity\PropertyType as TypeOfProperty;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type as FF;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Positive;

class PropertyType extends AbstractType
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
        $buttonLabel = $request->attributes->get('_route') === 'admin.properties.create' ? 'Enrégistrer' : 'Éditer';
        $pictureRequired = $request->attributes->get('_route') === 'admin.properties.create' ? true : false;

        $builder
            ->add('title', FF\TextType::class, [
                'label' => 'Titre',
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
            ->add('surface', FF\TextType::class, [
                'label' => 'Surface',
                /* 'constraints' => [
                    new Positive()
                ] */
            ])
            ->add('price')
            ->add('description')
            ->add('rooms')
            ->add('bedrooms')
            ->add('floor')
            ->add('adress')
            ->add('city')
            ->add('postal_code')
            ->add('sold')
            ->add('pictureFile', FileType::class, [
                'label' => 'Image',
                'required' => $pictureRequired,
                /* 'mapped' => false, */
            ])
            /* ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text'
            ]) */
            ->add('propertyType', EntityType::class, [
                'class' => TypeOfProperty::class,
                'choice_label' => 'name',
            ])
            ->add('heaters', EntityType::class, [
                'class' => Heating::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('specificities', EntityType::class, [
                'class' => Specificity::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('save', FF\SubmitType::class, [
                'label' => $buttonLabel,
                'attr' => [
                    'class' => 'btn btn-primary btn-sm'
                ]
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formFactoryListener->autoSlug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formFactoryListener->attachTimestamps(Property::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $validationGroups = ['Default', 'Extra'];
        if ($this->requestStack->getCurrentRequest()->attributes->get('_route') === 'admin.properties.create') {
            $validationGroups[] = 'pictureRequired';
        }

        $resolver->setDefaults([
            'data_class' => Property::class,
            'validation_groups' => $validationGroups
        ]);
    }

}
