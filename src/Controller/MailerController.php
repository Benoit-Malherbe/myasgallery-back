<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/email/")
 */
class MailerController extends AbstractController
{
    /**
     * @Route("/add", name="add_user_mail")
     */
    public function newAccountEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('myasgallerydev@gmail.com')
            ->to('morganetressens@gmail.com')
            ->subject('Création d\'un compte de gestionnaire')
            ->text('Félicitations ! Votre compte de gestionnaire de MyasGallery a bien été créé. Vous pouvez maintenant vous connecter au site via vos identifiants');

        $mailer->send($email);

        return $this->render('mailer/index.html.twig', [
            'mail' => $email,
        ]);
    }
}
