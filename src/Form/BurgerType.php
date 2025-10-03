<?php

namespace App\Form;

use App\Entity\Burger;
use App\Entity\Image;
use App\Entity\Oignon;
use App\Entity\Pain;
use App\Entity\Sauce;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BurgerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('pain', EntityType::class, [
                'class' => Pain::class,
                'choice_label' => 'id',
            ])
            ->add('oignon', EntityType::class, [
                'class' => Oignon::class,
                'choice_label' => 'id',
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('sauce', EntityType::class, [
                'class' => Sauce::class,
                'choice_label' => 'id',
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('image', EntityType::class, [
                'class' => Image::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Burger::class,
        ]);
    }
}
