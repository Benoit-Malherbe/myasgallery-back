<?php

namespace App\Controller\Api\V1;

use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

// creating a global route for artists BREAD

/**
* @Route("/api/v1/artiste", name="api_v1_artists_")
*/
class ArtistsController extends AbstractController
{

    // function browse is able to find a list of all artists and return this with json
    // we can set a limit or get random artists with custom request

    /**
    * @Route("", name="browse", methods={"GET"})
    */
    public function browse(ArtistRepository $artistRepository, Request $request): Response
    {
         $limit = (int) $request->get('limit');
         $random = (int) $request->get('random');
        
         if($limit) {
            $allArtists = $artistRepository->findBy(
                [],
                [],
                $limit
            );
          
         } elseif($random) {
            $allArtists = $artistRepository->findRandom($random);
         } else {
            $allArtists = $artistRepository->findAll();
         }

        
        return $this->json($allArtists, Response::HTTP_OK, [], ['groups' => 'api_artists_browse']);
    }


    // function read is able to find all informations about one artist and return this with json

    /**
    * @Route("/{slug}", name="read", methods={"GET"})
    */
    public function read(string $slug, ArtistRepository $artistRepository): Response
    {
        $selectedArtist = $artistRepository->findBy(["slug" => $slug]);
        
        if (is_null($selectedArtist)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($selectedArtist, Response::HTTP_OK, [], ['groups' => 'api_artists_browse']);
    }

    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource introuvable',
            'internalMessage' => 'Cet artiste n\'existe pas',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

}