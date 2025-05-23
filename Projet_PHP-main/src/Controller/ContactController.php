<?php

namespace App\Controller;

use App\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        
        $contact = new Contact();
            if ($this->getUser()) {
                $contact->setFullName($this->getUser()->getFullName());
                $contact->setEmail($this->getUser()->getEmail());
            }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $entityManager->persist($contact);
            $entityManager->flush();


            //email
        $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('Cr7@gmail.com')
            ->subject($contact->getSubject())
            ->htmlTemplate('emails/contact.html.twig')
            ->locale('de')
            ->context([
                'contact' => $contact 
            ]);

        $mailer->send($email);

            $this->addFlash('success', 'Votre message a été envoyé avec succès!');
            return $this->redirectToRoute('app_homepage');
    
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

