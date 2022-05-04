<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\User;
use Symfony\Component\Form\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateTimeType::class)
            ->add('user', EntityType::class, [
                'label' => 'Utilisateur',
                'class' => User::class,
            ])
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Create'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
