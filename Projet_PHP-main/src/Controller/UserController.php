<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Correct Interface
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route; // EntityManagerInterface for Symfony 6+

class UserController extends AbstractController
{
    #[Route('/admin/user/edit/{id}', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
       if(!$this->getUser()){
        return $this->redirectToRoute('app_login');
       }
       if ($this->getUser() !== $user) {
        return $this->redirectToRoute('app_homepage');
       } 
    
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) 
            {
                $user = $form->getData();
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Your account has been updated successfully!');

            }
           
            return $this->redirectToRoute('admin');
        }else 
        {
            $this->addFlash('warning', 'Password is incorrect');
        }
         return $this->render('user/edit.html.twig', [
        'form' => $form->createView(),
        'user' => $user,
     ]);
    }
}


