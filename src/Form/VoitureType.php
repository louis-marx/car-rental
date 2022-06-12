<?php

namespace App\Form;

use App\Entity\Parking;
use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'attr' => [
                    'placeholder' => 'Tapez le modèle du véhicule'
                ]
            ])
            ->add('immatriculation', TextType::class, [
                'label' => 'Immatriculation',
                'attr' => [
                    'placeholder' => 'Tapez l\'immatriculation du véhicule'
                ]
            ])
            ->add('carburant', ChoiceType::class, [
                'label' => 'Carburant',
                'attr' => [
                    'placeholder' => '-- Choisir un carburant --',
                ],
                'choices' => [
                    'essence' => 'gas',
                    'electrique' => 'electric',
                    'hybride' => 'hybrid',
                    'diesel' => 'diesel',
                ],
                'choice_label' => function ($choice, $key, $value) {
                    return strtoupper($key);
                }
            ])
            ->add('capaciteHabitacle', IntegerType::class, [
                'label' => 'Nombre de sièges',
                'attr' => [
                    'placeholder' => 'Tapez le nombre de sièges'
                ]
            ])
            ->add('boitier', ChoiceType::class, [
                'label' => 'Boite de vitesse',
                'attr' => [
                    'placeholder' => '-- Choisir une boite de vitesse --',
                ],
                'choices' => [
                    'manuelle' => 'manual',
                    'automatique' => 'automatic',
                ],
                'choice_label' => function ($choice, $key, $value) {
                    return strtoupper($key);
                }
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix du véhicule à la journée',
                'attr' => [
                    'placeholder' => 'Tapez le prix de la location en €'
                ],
                'divisor' => 100,
            ])
            ->add('image', UrlType::class, [
                'label' => 'Image du véhicule',
                'attr' => [
                    'placeholder' => 'Tapez une URL d\'image'
                ]
            ])
            ->add('parking', EntityType::class, [
                'label' => 'Parking',
                'placeholder' => '-- Choisir un parking --',
                'class' => Parking::class,
                'choice_label' => function (Parking $parking) {
                    return strtoupper($parking->getVille());
                },
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
