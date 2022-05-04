<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route("/", name: "homePage")]
    public function homePage() {
        return $this->render('homepage.html.twig');
    }

}