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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/backoffice/evenement/", name="backoffice_event_")
 */
class EventController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function browse(EventRepository $eventRepository): Response
    {

        $event = $eventRepository->findEventsWithAllInfos();

        return $this->render('backoffice/event/browse.html.twig', [
            'event_list' => $event,
        ]);
    }

    /**
     * @Route("add", name="add", methods={"GET", "POST"})
     * @IsGranted("ROLE_CATALOG_MANAGER")
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

            $this->addFlash('success', "L'événement {$event->getName()} a bien été ajouté");

            // Redirecting the user to be sure that the adding was done once
            return $this->redirectToRoute('backoffice_event_browse');
        }

        return $this->render('backoffice/event/editadd.html.twig', [
            'event_form' => $eventForm->createView(),
            'page' => 'add',
        ]);
    }

    /**
     * @Route("{slug}", name="read", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function read(Event $event, EventRepository $eventRepository): Response
    {
        
        $event = $eventRepository->findOneEventWithAllInfos($event->getSlug());

        return $this->render('backoffice/event/read.html.twig', [
            'current_event' => $event,
        ]);
    }

    /**
     * @Route("edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_CATALOG_MANAGER")
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

            $this->addFlash('success', "L'événement {$event->getName()} a bien été mis à jour");

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
    * @Route("delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
    * @IsGranted("ROLE_CATALOG_MANAGER")
    */
    public function delete(Event $event, EntityManagerInterface $entityManager): Response {
            
        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', "L'événement {$event->getName()} a bien été supprimé");
            
        return $this->redirectToRoute('backoffice_event_browse');
    }
}
