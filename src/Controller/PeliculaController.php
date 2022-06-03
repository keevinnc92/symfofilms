<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Pelicula;
use App\Form\PeliculaFormType;
use App\Form\PeliculaDeleteFormType;

use App\Service\FileService;



class PeliculaController extends AbstractController
{
    #[Route('/peliculas', name: 'pelicula_list')]
    public function list(ManagerRegistry $doctrine): Response{
    	$peliculas = $doctrine->getRepository(Pelicula::class)->findAll();
        return $this->render("pelicula/list.html.twig", ["peliculas" => $peliculas]);
    }

    #[Route('/pelicula/create', name: 'pelicula_create')]
    public function create(Request $request, ManagerRegistry $doctrine, FileService $uploader):Response{

            $pelicula = new Pelicula();

            $formulario = $this->createForm(PeliculaFormType::class, $pelicula); //creamos el formulario apartir de su FormType
            //en la respuesta enlazamos con el formulario 
            $formulario->handleRequest($request);

            if ($formulario->isSubmitted() && $formulario->isValid()) {

                $file = $formulario->get('caratula')->getData();
                // $file = $request->files->get('pelicula_form')['caratula'];
                // dd($file);
                if ($file) {
                    $uploader->targetDirectory = $this->getParameter('app.covers_root');
                    $pelicula->setCaratula($uploader->upload($file));
                }
                // dd($pelicula);die;
                //Guardamos los datos
                $entityManager = $doctrine->getManager();
                $entityManager->persist($pelicula);
                $entityManager->flush();

                //mensaje de notificación
                $this->addFlash('success', 'Película guardada con id '.$pelicula->getId());

                return $this->redirectToRoute('pelicula_show', ['id' => $pelicula->getId()]);
            }


            return $this->render('pelicula/create.html.twig',['formulario' =>$formulario->createView()]);
    }

    #[Route('/pelicula/{id<\d+>}', name: 'pelicula_show')]
    public function show(Pelicula $pelicula):Response{
        return $this->render("pelicula/show.html.twig", ["pelicula" => $pelicula]);
    }

    #[Route('/pelicula/edit/{id}', name: 'pelicula_edit')]
    public function edit(Pelicula $pelicula, Request $request, ManagerRegistry $doctrine, FileService $uploader):Response{

        $formulario = $this->createForm(PeliculaFormType::class, $pelicula);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            $file = $formulario->get('caratula')->getData();
            if ($file) {
                $uploader->targetDirectory = $this->getParameter('app.covers_root');
                $pelicula->setCaratula($uploader->upload($file));
            }

            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Película actualizada correctamente.');
            return $this->redirectToRoute('pelicula_show', ['id'=>$pelicula->getId()]);
        }

        return $this->render(
            "pelicula/edit.html.twig",
            ['formulario' =>$formulario->createView(),
            "pelicula" => $pelicula]
        );

    }

    #[Route('/pelicula/delete/{id}', name: 'pelicula_delete')]
    public function delete(Pelicula $pelicula, Request $request, ManagerRegistry $doctrine, FileService $uploader):Response{

        $formulario = $this->createForm(PeliculaDeleteFormType::class, $pelicula);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            if ($pelicula->getCaratula()) {

                $uploader->targetDirectory = $this->getParameter('app.covers_root');
                $uploader->remove($pelicula->getCaratula());
            }

            $entityManager = $doctrine->getManager();
            $entityManager->remove($pelicula);
            $entityManager->flush();

            $this->addFlash('success', 'Película eliminada correctamente.');

            return $this->redirectToRoute('pelicula_list');
        }
        
        return $this->render(
            "pelicula/delete.html.twig",
            ['formulario' =>$formulario->createView(),
            "pelicula" => $pelicula]
        );
    }

    #[Route('/pelicula/delete_cover/{id}', name: 'pelicula_delete_cover')]
    public function delete_cover(Pelicula $pelicula,  Request $request, ManagerRegistry $doctrine, FileService $uploader):Response{

        if ($pelicula->getCaratula()) {

            $uploader->targetDirectory = $this->getParameter('app.covers_root');
            $uploader->remove($pelicula->getCaratula());

            $pelicula->setCaratula(NULL);

            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $mensaje ='Caráctula de la película '.$pelicula->getTitulo().'eliminada correctamente.';
            $this->addFlash('success', $mensaje);

        }

        return $this->redirectToRoute('pelicula_edit', ['id' => $pelicula->getId()]);



    }


}
