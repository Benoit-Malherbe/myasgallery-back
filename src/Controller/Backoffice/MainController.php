<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/backoffice/", name="backoffice_main_")
 * @IsGranted("ROLE_USER")
 */
class MainController extends AbstractController
{
    /**
     * @Route("", name="show", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('backoffice/main/index.html.twig');
    }
}
