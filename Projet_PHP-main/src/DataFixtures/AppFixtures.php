<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Contact;
use App\Entity\Partenaire;

class AppFixtures extends Fixture
{
    
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    
    
    public function loadUsers(ObjectManager $manager): void
    {
        $faker = FakerFactory::create(); 

        
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setFullName($faker->name());

            // Optional pseudo logic
            if (mt_rand(0, 1) === 1) {
                $user->setPseudo($faker->firstName());
            }

            $user->setPlainPassword('password');
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setRoles(['ROLE_USER']);


            $manager->persist($user);
        }


        //COntact
        for ($i = 0; $i < 10; $i++) {
            $contact = new Contact();
            $contact->setFullName($faker->name());
            $contact->setEmail($faker->email());
        $contact->setSubject('Demande n°' . ($i + 1));
            $contact->setMessage($faker->text());
            $contact->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($contact);
        }

        $manager->flush();
    }
    public function load(ObjectManager $manager): void
    {
        // Ajouter des partenaires
        $this->loadPartenaires($manager);

        // Ajouter des utilisateurs
        $this->loadUsers($manager);
    }

    private function loadPartenaires(ObjectManager $manager): void
    {
        $partenaire1 = new Partenaire();
        $partenaire1->setNomPartenaire('Club X')
            ->setClubPartenaire('Club X')
            ->setTelPartenaire('123456789')
            ->setMailPartenaire('contact@clubx.com')
            ->setTypePartenaire('Club')
            ->setDatePartenariat(new \DateTime('2023-01-01'))
            ->setProjet('Projet A')
            ->setStatusPartenariat('Actif')
            ->setDateDebutContrat(new \DateTime('2023-01-01'))
            ->setDateFinContrat(new \DateTime('2024-01-01'))
            ->setContratActif(true);

        $manager->persist($partenaire1);

        $partenaire2 = new Partenaire();
        $partenaire2->setNomPartenaire('Entreprise Y')
            ->setClubPartenaire('Entreprise Y')
            ->setTelPartenaire('987654321')
            ->setMailPartenaire('contact@entreprisey.com')
            ->setTypePartenaire('Entreprise')
            ->setDatePartenariat(new \DateTime('2023-06-01'))
            ->setProjet('Projet B')
            ->setStatusPartenariat('Actif')
            ->setDateDebutContrat(new \DateTime('2023-06-01'))
            ->setDateFinContrat(new \DateTime('2024-06-01'))
            ->setContratActif(true);

        $manager->persist($partenaire2);

        $manager->flush();
    }
    
}
 