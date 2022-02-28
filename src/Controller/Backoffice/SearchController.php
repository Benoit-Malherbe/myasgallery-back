<?php

namespace App\Controller\Backoffice;

use App\Repository\ArtistRepository;
use App\Repository\ArtworkRepository;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/backoffice", name="backoffice_search")
 */
class SearchController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }
    
    // this function will sort elements by their name/title with each repository

    /**
     * @Route("/recherche", name="_bar")
     */
    public function searchBar(Request $request, ArtworkRepository $artworkRepository, ArtistRepository $artistRepository, CategoryRepository $categoryRepository, EventRepository $eventRepository): Response
    {
        $search = $request->query->get("search");
        $search = strtolower($search);
        $search = str_replace(' ','-', $search);

        
        $artwork = $artworkRepository->searchArtworks($search);
        $artist = $artistRepository->searchArtists($search);
        $category = $categoryRepository->searchCategories($search);
        $event = $eventRepository->searchEvents($search);

        $user = $this->security->getUser();
        
        if ($user == null) {
            return $this->redirectToRoute("login");
        } else if ($artwork || $artist || $category || $event) {
            return $this->render('backoffice/search/browse.html.twig', [
            'artwork_list' => $artwork,
            'artist_list' => $artist,
            'category_list' => $category,
            'event_list' => $event
            ]);
        } else {
            $this->addFlash('danger', 'Aucun résultat trouvé pour la recherche : ' . "{$request->query->get("search")}");
            return $this->redirectToRoute("backoffice_main_show");
        }
    }
}
