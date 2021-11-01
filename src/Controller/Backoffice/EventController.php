<?php

namespace App\Controller\Backoffice;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/evenement/", name="backoffice_event_")
 */
class EventController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(EventRepository $eventRepository): Response
    {
        return $this->render('backoffice/event/browse.html.twig', [
            'event_list' => $eventRepository->findAll(),
        ]);
    }

    /**
     * @Route("{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(Event $event): Response
    {
        return $this->render('backoffice/event/read.html.twig', [
            'current_event' => $event,
        ]);
    }

    /**
     * @Route("edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Event $event): Response
    {
        // Creation of an event form for edit the selected event
        $eventForm = $this->createForm(EventType::class, $event);
        
        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $event->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "L\'oeuvre `{$event->getName()}` a bien été mise à jour");

            // Redirecting the user to be sure that the edition was done once
            return $this->redirectToRoute('backoffice_event_browse');
        }

        return $this->render('backoffice/event/editadd.html.twig', [
            'event_form' => $eventForm->createView(),
            'event' => $event,
            'page' => 'edit',
        ]);
    }

    /**
     * @Route("add", name="add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response 
    {
        $event = new Event();

        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', "L\'oeuvre `{$event->getName()}` a bien été ajoutée");

            // Redirecting the user to be sure that the adding was done once
            return $this->redirectToRoute('backoffice_event_browse');
        }

        return $this->render('backoffice/event/editadd.html.twig', [
            'event_form' => $eventForm->createView(),
            'page' => 'add',
        ]);
    }

        /**
         * @Route("delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
         */
        public function delete(Event $event, EntityManagerInterface $entityManager): Response {
            
            $entityManager->remove($event);
            $entityManager->flush();

            $this->addFlash('success', "L\'oeuvre `{$event->getName()}` a bien été supprimée");
            
            return $this->redirectToRoute('backoffice_event_browse');
        }
}
