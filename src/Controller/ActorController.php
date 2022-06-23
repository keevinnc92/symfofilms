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
use Psr\Log\LoggerInterface;
use App\Form\SearchFormType;

use App\Service\FileService;

use App\Service\SimpleSearchService;

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

    #[Route('/actor/search}', name: 'actor_search', methods: ['GET', 'POST'])]
    public function search(Request $request, SimpleSearchService $busqueda):Response{

        // crea el formulario
        $formulario = $this->createForm(SearchFormType::class, $busqueda, [
            'field_choices' => [
                'Nombre' => 'nombre',
                'Fecha nacimiento' => 'fecha_nacimiento',
                'Nacionalidad' => 'nacionalidad',
                'Biografía' => 'biografia'
            ],
            'order_choices' => [
                'ID' => 'id',
                'Nombre' => 'nombre',
                'Nacionalidad' => 'nacionalidad',
                'Biografía' => 'biografia'
            ]
        ]);

        $formulario->get('campo')->setData($busqueda->campo);
        $formulario->get('orden')->setData($busqueda->orden);

        // gestiona el formulario y recupera los valores de búsqueda
        $formulario->handleRequest($request);

        //realiza la búsqueda
        $actores = $busqueda->search('App\Entity\Actor');

        //retorna la vista con los resultados
        return $this->renderForm("actor/buscar.html.twig", [
            "formulario" => $formulario,
            "actores" => $actores
        ]);

    }


    #[Route('/actor/create', name: 'actor_create', methods: ['GET', 'POST'])]
    public function create(Request $request, LoggerInterface $appInfoLogger, ActorRepository $actorRepository): Response{
        $actor = new Actor();
        $formulario = $this->createForm(ActorFormType::class, $actor);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $actorRepository->add($actor, true);
            $mensaje = "Actor con id:".$actor->getId()." guardado correctamente";
            $appInfoLogger->info($mensaje);

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
    public function delete(Actor $actor, Request $request, LoggerInterface $appInfoLogger, ManagerRegistry $doctrine, FileService $uploader):Response{

        $formulario = $this->createForm(ActorDeleteFormType::class, $actor);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // if ($pelicula->getCaratula()) {

            //     $uploader->targetDirectory = $this->getParameter('app.covers_root');
            //     $uploader->remove($pelicula->getCaratula());
            // }
            $id_actor = $actor->getId();

            $entityManager = $doctrine->getManager();
            $entityManager->remove($actor);
            $entityManager->flush();


            $mensaje = "Actor con id:".$id_actor." eliminado correctamente";

            $appInfoLogger->info($mensaje);

            $this->addFlash('success', $mensaje);

            return $this->redirectToRoute('actor_list');
        }
        
        return $this->render(
            "actor/delete.html.twig",
            ['formulario' =>$formulario->createView(),
            "actor" => $actor]
        );
    }


    
}
