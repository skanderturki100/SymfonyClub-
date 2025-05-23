<?php
// src/Form/ReservationType.php
namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Reservation;
use App\Entity\Ressources;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;



class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void

    {



        $builder
        ->add('dateDebut', DateTimeType::class, [
            'widget' => 'single_text',
            'html5' => true,
            'required' => true,
            'label' => 'Date de début',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Assert\NotBlank(['message' => 'La date de début est obligatoire.']),
                new Assert\GreaterThanOrEqual([
                    'value' => 'today',
                    'message' => 'La date de début doit être supérieure ou égale à aujourd\'hui.',
                ]),
            ],
        ])


        ->add('dateFin', DateTimeType::class, [
            'widget' => 'single_text',
            'html5' => true,
            'required' => true,
            'label' => 'Date de fin',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Assert\NotBlank(['message' => 'La date de fin est obligatoire.']),
                new Assert\GreaterThanOrEqual([
                    'value' => 'today',
                    'message' => 'La date de fin doit être supérieure ou égale à aujourd\'hui.',
                ]),
                
            ],
        ])

        ->add('motif', TextareaType::class, [
            'required' => true,
            'label' => 'Motif de la réservation',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Assert\NotBlank(['message' => 'Le motif est obligatoire.']),
                new Assert\Length([
                    'min' => 5,
                    'max' => 255,
                    'minMessage' => 'Le motif doit comporter au moins {{ limit }} caractères.',
                    'maxMessage' => 'Le motif ne peut pas dépasser {{ limit }} caractères.',
                ]),
            ],
        ])
        
        ->add('ressource', EntityType::class, [
            'class' => Ressources::class,
            'choice_label' => 'nom',  // Affiche le nom de la ressource
            'required' => true,
            'disabled' => true, // Désactive le champ de ressource pour empêcher toute modification
            'data' => $options['data']->getRessource(), // Pré-remplir le champ avec la ressource sélectionnée
            'label' => 'Ressource',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Assert\NotBlank(['message' => 'La ressource est obligatoire.']),
            ],
        ])

        ->add('quantite', IntegerType::class, [
            'label' => 'Quantité à réserver',
            'attr' => ['min' => 1], // Optionnel, mais limite la valeur minimale à 1
        ])

        


            // Ajouter le bouton "Enregistrer"
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);

            
            // // ->add('captcha', Recaptcha3Type::class, [
            // //     'constraints' => new Recaptcha3(),
            // //     'action_name' => 'reservation',
                
            // ]);
           
           
    }   


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
