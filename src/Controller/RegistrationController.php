<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription/', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('home');
            $this->addFlash('error', 'Vous êtes déjà connecté!');
        } else {

            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $email = (new TemplatedEmail())
                ->from('support@lhannz.fr')
                ->to($form['email']->getData(), 'anishamouche@gmail.com', 'support@lhannz.fr')
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Imaginary Conception')
                // ->textTemplate('emails/registeremail.txt.twig')
                ->htmlTemplate('emails/registeremail.html.twig')
                ->context([
                    'form_email' => $form['email']->getData(),
                ]);
                $mailer->send($email);
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                $this->addFlash('success', 'Votre compte a bien été créé !');
                return $this->redirectToRoute('home');
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }
    }
}
