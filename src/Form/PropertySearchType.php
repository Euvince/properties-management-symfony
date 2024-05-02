<?php

namespace App\Form;

use App\Entity\PropertySearch;
use App\Entity\Specificity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as FF;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('maxPrice', FF\NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => '',
                    'placeholder' => 'Entrez un prix maximal'
                ]
            ])
            ->add('minSurface', FF\NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => '',
                    'placeholder' => 'Entrez une surface minimale'
                ]
            ])
            ->add('specificities', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Specificity::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => [
                    'class' => '',
                    'placeholder' => 'Choisissez des otpions'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropertySearch::class,
            'validation_groups' => ['Default'],
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix() : string
    {
        return '';
    }

}
