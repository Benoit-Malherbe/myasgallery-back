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

/**
 * @Route("/backoffice", name="backoffice_search")
 */
class SearchController extends AbstractController
{

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

        if ($artwork) {
            return $this->render('backoffice/artwork/browse.html.twig', [
            'artwork_list' => $artwork,
            ]);
        } elseif ($artist) {
            return $this->render('backoffice/artist/browse.html.twig', [
            'artist_list' => $artist,
            ]);
        } elseif ($category) {
            return $this->render('backoffice/category/browse.html.twig', [
            'category_list' => $category,
            ]);
        } elseif ($event) {
            return $this->render('backoffice/event/browse.html.twig', [
            'event_list' => $event,
            ]);
        } else {
            $this->addFlash('danger', 'Aucun résultat trouvé pour la recherche : ' . "{$request->query->get("search")}");
            return $this->redirectToRoute("backoffice_main_show");
        }
    }
}
