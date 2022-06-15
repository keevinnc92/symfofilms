<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/home', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
    	// rechaza usuarios no identificados (FULLY=completos, que no sean por el check)
    	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('user/home.html.twig');
    }
}
