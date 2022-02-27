<?php

namespace App\Controller\Backoffice;

use App\Entity\Artist;
use App\Form\ArtistType;
use App\Repository\ArtistRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/backoffice/artiste/", name="backoffice_artist_")
 */
class ArtistController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function browse(ArtistRepository $artistRepository): Response
    {
        $artist = $artistRepository->findArtistsWithAllInfos();

        return $this->render('backoffice/artist/browse.html.twig', [
            'artist_list' => $artist,
        ]);
    }

    /**
     * @Route("add", name="add", methods={"GET", "POST"})
     * @IsGranted("ROLE_CATALOG_MANAGER")
     */
    public function add(Request $request): Response
    {
        $artist = new Artist;

        $artistForm = $this->createForm(ArtistType::class, $artist);
        $artistForm->handleRequest($request);

        if ($artistForm->isSubmitted() && $artistForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($artist);
            $entityManager->flush();

            $this->addFlash('success', "L'artiste {$artist->getName()} a bien été ajouté");

            return $this->redirectToRoute('backoffice_artist_browse');
        }
        
        return $this->render('backoffice/artist/editadd.html.twig', [
            'artist_form' => $artistForm->createView(),
            'page' => 'add',
        ]);
    }

    /**
     * @Route("{slug}", name="read", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function read(Artist $artist, ArtistRepository $artistRepository): Response
    {

        $artist = $artistRepository->findOneArtistWithAllInfos($artist->getSlug());

        return $this->render('backoffice/artist/read.html.twig', [
            'current_artist' => $artist,
        ]);
    }

    /**
     * @Route("edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_CATALOG_MANAGER")
     */
    public function edit(Request $request,Artist $artist): Response
    {
        $artistForm = $this->createForm(ArtistType::class, $artist);
        $artistForm->handleRequest($request);

        if ($artistForm->isSubmitted() && $artistForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $artist->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "L'artiste {$artist->getName()} a bien été mis à jour");

            return $this->redirectToRoute('backoffice_artist_browse');
        }
        
        return $this->render('backoffice/artist/editadd.html.twig', [
            'artist_form' => $artistForm->createView(),
            'artist' => $artist,
            'page' => 'edit',
        ]);
    }

     /**
      * @Route("delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
      * @IsGranted("ROLE_CATALOG_MANAGER")
      */
    public function delete(Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($artist);
        $entityManager->flush();

        $this->addFlash('success', "L'artiste {$artist->getName()} a bien été supprimé");

        return $this->redirectToRoute('backoffice_artist_browse');
    }
}
