<?php

namespace App\Controller;



use App\Entity\Actor;
use App\Repository\ActorRepository;
use App\Form\ActorFormType;
use App\Form\ActorDeleteFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\FileService;

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

    #[Route('/actor/edit/{id}', name: 'actor_edit')]
    public function edit(Actor $actor, Request $request, ManagerRegistry $doctrine, FileService $uploader):Response{

        $formulario = $this->createForm(ActorFormType::class, $actor);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // $file = $formulario->get('caratula')->getData();
            // if ($file) {
            //     $uploader->targetDirectory = $this->getParameter('app.covers_root');
            //     $pelicula->setCaratula($uploader->upload($file));
            // }

            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Actor actualizado correctamente.');
            return $this->redirectToRoute('actor_show', ['id'=>$actor->getId()]);
        }

        return $this->render(
            "actor/edit.html.twig",
            ['formulario' =>$formulario->createView(),
            "actor" => $actor]
        );

    }

    #[Route('/actor/delete/{id}', name: 'actor_delete')]
    public function delete(Actor $actor, Request $request, ManagerRegistry $doctrine, FileService $uploader):Response{

        $formulario = $this->createForm(ActorDeleteFormType::class, $actor);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // if ($pelicula->getCaratula()) {

            //     $uploader->targetDirectory = $this->getParameter('app.covers_root');
            //     $uploader->remove($pelicula->getCaratula());
            // }

            $entityManager = $doctrine->getManager();
            $entityManager->remove($actor);
            $entityManager->flush();

            $this->addFlash('success', 'Actor eliminado correctamente.');

            return $this->redirectToRoute('actor_list');
        }
        
        return $this->render(
            "actor/delete.html.twig",
            ['formulario' =>$formulario->createView(),
            "actor" => $actor]
        );
    }


    
}
