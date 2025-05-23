<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener{

    private UserPasswordHasherInterface $Hasher;
    public function __construct(UserPasswordHasherInterface $Hasher){
        $this->Hasher = $Hasher;
    }
    public function prePersist(User $user){
       $this->encodePassword($user);
    }
    public function preUpdate(User $user){
        $this->encodePassword($user);

    }

    /**
     * Summary of encodePassword
     * @param \App\Entity\User $user
     * @return void
     */
    public function encodePassword(User $user){

        if ($user->getPlainPassword() === null) {
            return;
        }
    
        $hashedPassword = $this->Hasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($hashedPassword); // Ensure you set the hashed password
        $user->setPlainPassword(null); // Clear the plain password
}
}