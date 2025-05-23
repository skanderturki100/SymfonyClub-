<?php

namespace App\Form;

use App\Entity\Club;
use App\Entity\FeedBacks;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Range;


class FeedBackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statusFeedback', ChoiceType::class, [
                'label' => 'Status',
                'required' => false,
                'choices' => [
                    'Pending' => 'pending',
                    'Active' => 'active',
                    'Inactive' => 'inactive',
                ],
                'data' => 'pending',
                'constraints' => [
                    new NotBlank(['message' => 'Please select a status.']),
                ],
                'attr' => ['style' => 'display:none;'],
                'label_attr' => ['style' => 'display:none;'],
                
            ])
            ->add('dateFeedback', null, [
                'widget' => 'single_text',
                'label' => 'Feedback Date',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'The creation date cannot be in the past.',
                    ]),
                ],
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Comment',
                'attr' => ['placeholder' => 'Enter your comment'],
                'constraints' => [
                    new NotBlank(['message' => 'Comment cannot be empty.']),
                    new Length([
                        'min' => 10,
                        'max' => 500,
                        'minMessage' => 'The comment must be at least {{ limit }} characters long.',
                        'maxMessage' => 'The comment cannot exceed {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('idClub', EntityType::class, [
                'class' => Club::class,
                'choice_label' => 'nomClub', // Assuming 'nomClub' is the field to display
                'placeholder' => 'Select a club', // Optional placeholder
                'constraints' => [
                    new NotBlank(['message' => 'Please select a club.']),
                ],
            ])
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName', // Assuming 'nomPersonne' is the field to display
                'placeholder' => 'Select a person', // Optional placeholder
                'constraints' => [
                    new NotBlank(['message' => 'Please select a person.']),
                ],
            ])
            ->add('avis', ChoiceType::class, [
                'label' => 'Rating',
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
                'expanded' => true, // Render as radio buttons
                'multiple' => false, // Single choice
                'constraints' => [
                    new NotBlank(['message' => 'Please provide a rating.']),
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'Rating must be between {{ min }} and {{ max }}.',
                    ]),
                ],
            ]);
            // ->add('submit', SubmitType::class, [
            //     'label' => 'Save',
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FeedBacks::class,
        ]);
    }
}

