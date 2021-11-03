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

/**
 * @Route("/backoffice/categorie/", name="backoffice_category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(CategoryRepository $categoryRepository): Response
    {
        $allCategory = $categoryRepository->findAll();

        return $this->render('backoffice/category/browse.html.twig', [
            'category_list' => $allCategory,
        ]);
    }

    /**
     * @Route("{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(Category $category): Response
    {
        return $this->render('backoffice/category/read.html.twig', [
        'category' => $category,
        ]);
    }

    /**
     * @Route("edit/{id}", name="edit", methods={"GET", "POST"}), requirements={"id"="\d+"})
     */
    public function edit(Request $request,Category $category): Response
    {
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $category->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "La catégorie {$category->getName()} a bien été mis à jour");

            return $this->redirectToRoute('backoffice_category_browse');
        }

        return $this->render('backoffice/category/editadd.html.twig', [
            'category_form' => $categoryForm->createView(),
            'category' => $category,
            'page' => 'edit',
        ]);
    }

    /**
     * @Route("add", name="add", methods={"GET", "POST"})
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

            $this->addFlash('success', "La catégorie {$category->getName()} a bien été ajouté");

            return $this->redirectToRoute('backoffice_category_browse');
        }
        
        return $this->render('backoffice/category/editadd.html.twig', [
            'category_form' => $categoryForm->createView(),
            'page' => 'add',
        ]);
    }

    /**
     * @Route("delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function delete(Category $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', "La catégorie {$category->getName()} a bien été supprimé");

        return $this->redirectToRoute('backoffice_category_browse');
    }
}