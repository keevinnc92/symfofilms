<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

class DummyDQLController extends AbstractController
{
    // #[Route('/dummy/d/q/l', name: 'app_dummy_d_q_l')]
    // public function index(): Response
    // {
    //     return $this->render('dummy_dql/index.html.twig', [
    //         'controller_name' => 'DummyDQLController',
    //     ]);
    // }


    #[Route('dummy/dql1')]
    public function dql1(EntityManagerInterface $em){
        $peliculas = $em->createQuery(
            'SELECT p --vamos a seleccionar
            FROM App\Entity\Pelicula p --películas
            WHERE p.valoracion >1 --con valoración > 1 
            ORDER BY p.valoracion DESC --ordenadas de mejor a peor')
        ->getResult();

        $respuesta = implode('<br>', $peliculas);
        return new Response($respuesta);
    }



    #[Route('dummy/dqllimit')]
    public function dqllimit(EntityManagerInterface $em){
        $peliculas = $em->createQuery(
            'SELECT p 
            FROM App\Entity\Pelicula p 
            ORDER BY p.id DESC')
        ->setMAxResults(5) //limit
        ->setFirstResult(0) //offset
        ->getResult();

        $respuesta = implode('<br>', $peliculas);
        return new Response($respuesta);
    }

}
