<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    /**
     * formulaire de contact
     *
     * @param request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/contact', name: 'contact.index')]
    public function index(
        Request $request,
        EntityManagerInterface $manager,
        MailerInterface $mailer): Response
    {
        $contact = new Contact();
        // je verifie si j'ai bien un utilisateur connecté
        if($this->getUser())
        {
            $contact->setFullName($this->getUser()->getFullName())
                ->setEmail($this->getUser()->getEmail());
        }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $contact = $form->getData();
            $manager->persist($contact);
            $manager->flush();
            
             $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('admin@recette.com')
            ->subject($contact->getSubject())
            ->htmlTemplate('pages/emails/contact.html.twig')

            // pass variables contact ($contact) to the template contact.html.twig
            ->context([
                'contact' => $contact
            ]);

             $mailer->send($email);
            $this->addFlash(
                'success',
                'Votre demande a été envoyé avec succès!'
            );
    
        /* on précise la redirection*/
        return $this->redirectToRoute('contact.index');
        }
            
        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}