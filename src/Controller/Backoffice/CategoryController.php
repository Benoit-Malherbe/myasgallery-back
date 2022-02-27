<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/backoffice/categorie/", name="backoffice_category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function browse(CategoryRepository $categoryRepository): Response
    {

        $category = $categoryRepository->findCategoriesWithAllInfos();

        return $this->render('backoffice/category/browse.html.twig', [
            'category_list' => $category,
        ]);
    }

    /**
     * @Route("add", name="add", methods={"GET", "POST"})
     * @IsGranted("ROLE_CATALOG_MANAGER")
     */
    public function add(Request $request): Response
    {
        $category = new Category;

        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', "La catégorie {$category->getName()} a bien été ajoutée");

            return $this->redirectToRoute('backoffice_category_browse');
        }
        
        return $this->render('backoffice/category/editadd.html.twig', [
            'category_form' => $categoryForm->createView(),
            'page' => 'add',
        ]);
    }

    /**
     * @Route("{slug}", name="read", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function read(Category $category, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneCategoryWithAllInfos($category->getSlug());

        return $this->render('backoffice/category/read.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("edit/{id}", name="edit", methods={"GET", "POST"}), requirements={"id"="\d+"})
     * @IsGranted("ROLE_CATALOG_MANAGER")
     */
    public function edit(Request $request,Category $category): Response
    {
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $category->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "La catégorie {$category->getName()} a bien été mise à jour");

            return $this->redirectToRoute('backoffice_category_browse');
        }

        return $this->render('backoffice/category/editadd.html.twig', [
            'category_form' => $categoryForm->createView(),
            'category' => $category,
            'page' => 'edit',
        ]);
    }

    /**
     * @Route("delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_CATALOG_MANAGER")
     */
    public function delete(Category $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', "La catégorie {$category->getName()} a bien été supprimée");

        return $this->redirectToRoute('backoffice_category_browse');
    }
}
