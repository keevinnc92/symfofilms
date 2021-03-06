<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use App\Form\ContactoFormType;

use Symfony\Component\Mime\Email;

use App\Service\FrasesService;
use App\Entity\Pelicula;


class DefaultController extends AbstractController
{
    #[Route('/', name: 'portada')]
    public function index(FrasesService $frases,  ManagerRegistry $doctrine): Response
    {

        $repository = $doctrine->getRepository(Pelicula::class);
        $last_peliculas = $repository->novedadesPeliculas();


        return $this->render('portada.html.twig', [
            // 'controller_name' => 'DefaultController',
            'frase' => $frases->getFraseAleatoria(),
            'peliculas' => $last_peliculas,

        ]);
    }

    #[Route('/contacto', name: 'contacto')]
    public function contacto(Request $request, MailerInterface $mailer): Response{

    	$formulario = $this->CreateForm(ContactoFormType::class);
    	$formulario->handleRequest($request);

    	if ($formulario->isSubmitted() && $formulario->isValid()) {
    		$datos = $formulario->getData();

    		$email = new Email();
    		$email->from($datos['email'])
    			->to($this->getParameter('app.admin_email')) //viene de services.yaml
    			->subject($datos['asunto'])
    			->text($datos['mensaje']);

    		$mailer->send($email);
    		
    		$this->addFlash('success', 'Mensaje enviado correctamente');
    		return $this->redirectToRoute('portada');
    	}
    	
    	return $this->renderForm("contacto.html.twig", ["formulario"=>$formulario]);

    }
}
