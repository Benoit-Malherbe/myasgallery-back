<?php

namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ContactController extends AbstractController
{

    // this route will send an email to a user who sent a message with the contact form

    /**
     * @Route("/api/v1/contact", name="contact", methods={"POST"})
     */
    public function sendMessage(Request $request, MailerInterface $mailer): Response
    {
        $requestDatasArray = json_decode($request->getContent(), true);
        // on vérifie que le champ pot de miel est vide
        if(isset($requestDatasArray['pseudo']) && empty($requestDatasArray['pseudo']) && isset($requestDatasArray['email1']) && empty($requestDatasArray['email1'])){
            // on vérifie que tous les champs sont présents et remplis
            if(
                isset($requestDatasArray['lastname']) && !empty($requestDatasArray['lastname']) &&
                isset($requestDatasArray['firstname']) && !empty($requestDatasArray['firstname']) &&
                isset($requestDatasArray['company']) &&
                isset($requestDatasArray['email']) && !empty($requestDatasArray['email']) &&
                isset($requestDatasArray['phone']) && !empty($requestDatasArray['phone']) &&
                isset($requestDatasArray['messageObject']) && !empty($requestDatasArray['messageObject']) &&
                isset($requestDatasArray['message']) && !empty($requestDatasArray['message'])
            ){
                // on nettoie le contenu
                $lastname = strip_tags($requestDatasArray['lastname']);
                $firstname = strip_tags($requestDatasArray['firstname']);
                $company = strip_tags($requestDatasArray['company']);
                $mail = strip_tags($requestDatasArray['email']);
                $phone = strip_tags($requestDatasArray['phone']);
                $messageObject = strip_tags($requestDatasArray['messageObject']);
                $message = strip_tags($requestDatasArray['message']);

                // envoie du mail
                $email = (new TemplatedEmail())
                    ->from('myasgallerydev@gmail.com')
                    ->to($mail)
                    ->subject($messageObject)
                    ->text($message)
                    ->htmlTemplate('contact/index.html.twig')
                    ->context([
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'messageObject' => $messageObject,
                        'message' => $message,
                        'company' => $company,
                        'phone' => $phone,
                        'mail' => $mail
                    ]);

                $mailer->send($email);

                return $this->json('all data sent successfully', 200);
            }

        }else{
            return $this->json('forbidden', 403);
        }
        
    }
}