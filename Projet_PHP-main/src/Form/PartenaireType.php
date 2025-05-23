<?php

namespace App\Form;

use App\Entity\Partenaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PartenaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomPartenaire', TextType::class, [
                'label' => 'Nom du partenaire',
            ])
            ->add('clubPartenaire', TextType::class, [
                'label' => 'Club partenaire',
            ])
            ->add('telPartenaire', TelType::class, [
                'label' => 'Téléphone',
            ])
            ->add('mailPartenaire', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('typePartenaire', TextType::class, [
                'label' => 'Type de partenaire',
            ])
            ->add('datePartenariat', DateType::class, [
                'label' => 'Date du partenariat',
                'widget' => 'single_text',
            ])
            ->add('projet', TextType::class, [
                'label' => 'Projet',
            ])
            ->add('statusPartenariat', TextType::class, [
                'label' => 'Statut du partenariat',
            ])
            ->add('dateDebutContrat', DateType::class, [
                'label' => 'Date début du contrat',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('dateFinContrat', DateType::class, [
                'label' => 'Date fin du contrat',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('contratActif', CheckboxType::class, [
                'label' => 'Contrat actif',
                'required' => false,
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partenaire::class,
        ]);
    }
}
