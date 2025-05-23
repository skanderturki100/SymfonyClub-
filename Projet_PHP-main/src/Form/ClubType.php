<?php

namespace App\Form;

use App\Entity\Club;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
class ClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomClub', TextType::class, [
                'label' => 'Club Name',
                'attr' => ['placeholder' => 'Enter the club name'],
                'constraints' => [
                    new NotBlank(['message' => 'The club name cannot be blank.']),
                    new Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'The club name must be at least {{ limit }} characters.',
                        'maxMessage' => 'The club name cannot exceed {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('categorieClub', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Academic clubs' => 'Academic clubs',
                    'Arts clubs' => 'Arts clubs',
                    'Cultural clubs' => 'Cultural clubs',
                    'Sports clubs' => 'Sports clubs',
                    'Fitness clubs' => 'Fitness clubs',
                    'Hobby clubs' => 'Hobby clubs',
                    'Technology clubs' => 'Technology clubs',
                    'Music clubs' => 'Music clubs',
                    'Dance clubs' => 'Dance clubs',
                    'Drama clubs' => 'Drama clubs',
                    'Language clubs' => 'Language clubs',
                    'Debate clubs' => 'Debate clubs',
                    'Photography clubs' => 'Photography clubs',
                    'Environmental clubs' => 'Environmental clubs',
                    'Volunteer clubs' => 'Volunteer clubs',
                    'Gaming clubs' => 'Gaming clubs',
                    'Literary clubs' => 'Literary clubs',
                    'Entrepreneurial clubs' => 'Entrepreneurial clubs',
                    'Science clubs' => 'Science clubs',
                    'Travel clubs' => 'Travel clubs',
                ],
                'attr' => ['placeholder' => 'Select a category'],
                'constraints' => [
                    new NotBlank(['message' => 'Please select a category.']),
                ],
            ])
            ->add('dateCreationClub', DateType::class, [
                'label' => 'Creation Date',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'Select the creation date'],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'The creation date cannot be in the past.',
                    ]),
                ],
            ])
            ->add('adresseClub', TextType::class, [
                'label' => 'Location',
                'attr' => ['placeholder' => 'Enter the club location'],
                'constraints' => [
                    new NotBlank(['message' => 'The location cannot be blank.']),
                ],
            ])
            ->add('telClub', TelType::class, [
                'label' => 'Phone Number',
                'attr' => ['placeholder' => 'Enter the phone number'],
                'constraints' => [
                    new NotBlank(['message' => 'The phone number cannot be blank.']),
                    new Regex([
                        'pattern' => '/^\+?[0-9]{7,15}$/',
                        'message' => 'The phone number must contain only digits and can optionally start with a "+".',
                    ]),
                ],
            ])
            ->add('siteWeb', UrlType::class, [
                'label' => 'Website',
                'attr' => ['placeholder' => 'Enter the website URL'],
                'constraints' => [
                    new Url(['message' => 'Please enter a valid URL.']),
                ],
            ])
            ->add('statusClubs', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Active' => 'active',
                    'Inactive' => 'inactive',
                    'Pending' => 'pending',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please select a status.']),
                ],
            ])
            ->add('attribute5', TextareaType::class, [
                'label' => 'Additional Info',
                'required' => false,
                'attr' => ['placeholder' => 'Any additional information'],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Additional information cannot exceed {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('photoFile', VichImageType::class, [
                'label' => 'Club Photo (jpg, png, etc.)',
                'mapped' => true,  // This means the file is not mapped directly to the entity, VichUploader will handle it
                'required' => false,  // If you want to make this field optional, set it to false
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',  // Max file size (1MB)
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/jfif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (jpg, png, gif, etc.)',
                    ]),
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Save Club',
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Club::class,
        ]);
    }
}

