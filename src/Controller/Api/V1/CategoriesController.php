<?php

namespace App\Controller\Api\V1;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// creating a global route for categories BREAD
/**
 * @Route("/api/v1/categorie", name="api_v1_categories_")
 */
class CategoriesController extends AbstractController
{
    // function browse is able to find a list of all categories and return this with json
    // we can set a limit or get random categories with custom request

    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(CategoryRepository $categoryRepository, Request $request): Response
    {
        $limit = (int) $request->get('limit');
         $random = (int) $request->get('random');
        
         if($limit) {
            $allCategories = $categoryRepository->findBy(
                [],
                [],
                $limit
            );
          
         } elseif($random) {
            $allCategories = $categoryRepository->findRandom($random);
         } else {
            $allCategories = $categoryRepository->findAll();
         }

        return $this->json($allCategories, Response::HTTP_OK, [], ['groups' => 'api_category_browse']);
    }

    // function read is able to find all informations about one category and return this with json
    
    /**
     * @Route("/{slug}", name="read", methods={"GET"})
     */
    public function read(string $slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findBy(["slug" => $slug]);

        if (is_null($category)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($category, Response::HTTP_OK, [], ['groups' => 'api_category_browse']);
    }

    private function getNotFoundResponse() {

        $localTest = "test";
        $globalTest = $localTest;

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouv??',
            'internalMessage' => 'Ce category n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
