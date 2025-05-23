<?php
namespace App\Form;

use ApiPlatform\Symfony\Validator\Validator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Image;
use App\Entity\Ressources;
use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints\ValidCaptcha;

class RessourcesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // Champ 'Nom' de la ressource
        ->add('nom', TextType::class, [
            'label' => '',
            'required' => true,
            'attr' => [
                'class' => 'form-control custom-input', // Classe Bootstrap et CSS personnalisé
                'placeholder' => 'Entrez le nom de la ressource',
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom ne peut pas être vide.',
                ]),
                new Assert\Length([
                    'min' => 3, 
                    'max' => 255,
                    'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                    'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                ]),
            ],
        ])

        // Champ 'Description' de la ressource
        ->add('description', TextareaType::class, [
            'label' => '',
            'required' => true,
            'attr' => [
                'class' => 'form-control custom-input',
                'placeholder' => 'Entrez une description détaillée de la ressource', 
                'rows' => 5,
            ],
            'constraints' => [
                new Assert\Length([
                    'min' => 10, 
                    'max' => 500, 
                    'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                    'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                ]),
            ],
        ])



        // Champ 'Quantité' de la ressource
        ->add('quantite',   IntegerType::class, [
            'attr' => [
                'class' => 'form-control custom-input', 
                'min' => 1, 
                'type' => 'number',
                'placeholder' => 'Quantité de la ressource', 
            ],
            'constraints' => [
                new Assert\Positive([
                    'message' => 'La quantité doit être strictement supérieure à zéro.',
                ]),
            ],
        ])




        // Champ 'État' de la ressource
        ->add('etat', ChoiceType::class, [
            'label' => '',
            'choices'  => [
                'En Marche' => 'En Marche',
                'En Maintenance' => 'maintenance',
            ],
            'expanded' => true, 
            'multiple' => false,
            'required' => true,
            'data' => 'En Marche',
        ])

        // Champ 'Photo' de la ressource
        ->add('photo', FileType::class, [
            'label' => '',
            'required' => $options['is_new'],
            'mapped' => false,
            'attr' => [
                'class' => 'form-control-file custom-input', 
            ],


            
            'constraints' => $options['is_new'] ? [
                new NotBlank(['message' => 'La photo est obligatoire.']),
                new Image([
                    'maxWidth' => 5000,
                    'maxHeight' => 5000,
                    'maxSize' => '50M',
                    'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                    'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, ou GIF).',
                ]),
            ] : [],
        ])

        // Bouton de soumission
        ->add('submit', SubmitType::class, [
            'label' => 'Enregistrer',
            'attr' => [
                'class' => 'btn btn-primary custom-btn mt-3',
            ]
            ]);

            // ->add('captcha', Recaptcha3Type::class, [
            //     'constraints' => new Recaptcha3(),
            //     'action_name' => 'resource',
            //     'locale' => 'de',
            // ])
           
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ressources::class,
            'is_new' => false,
        ]);
    }
}
