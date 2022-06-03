<?php

namespace App\Controller;



use App\Entity\Actor;
use App\Repository\ActorRepository;
use App\Form\ActorFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ActorController extends AbstractController
{
    #[Route('/actor', name: 'app_actor')]
    public function index(): Response
    {
        return $this->render('actor/index.html.twig', [
            'controller_name' => 'ActorController',
        ]);
    }


    #[Route('/actores', name: 'actor_list')]
    public function list(ActorRepository $actorRepository): Response{
    	$actores = $actorRepository->findAll();
        return $this->render("actor/list.html.twig", ["actores" => $actores]);
    }


    #[Route('/actor/create', name: 'actor_create', methods: ['GET', 'POST'])]
    public function create(Request $request, ActorRepository $actorRepository): Response{
        $actor = new Actor();
        $formulario = $this->createForm(ActorFormType::class, $actor);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $actorRepository->add($actor, true);

            return $this->redirectToRoute('actor_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('actor/create.html.twig', [
            'actor' => $actor,
            'formulario' => $formulario,
        ]);
    }

    #[Route('/actor/{id<\d+>}', name: 'actor_show')]
    public function show(Actor $actor):Response{
        return $this->render("actor/show.html.twig", ["actor" => $actor]);
    }

    #[Route('/actor/edit/id<d+>}', name: 'actor_edit')]
    public function edit(){

    }


    
}
