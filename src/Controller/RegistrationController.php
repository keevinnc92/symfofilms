<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserDeleteFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\RequestStack;


use App\Service\FileService;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager, FileService $uploader): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //SUBIDA IMG RETRATO
            $uploader->targetDirectory = $this->getParameter('app.users_pics_root');
            $file = $form->get('fotografia')->getData();
            if ($file) {
                $user->setFotografia($uploader->upload($file));
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('register@symfofilms.com', 'Registro de usuarios'))
                    ->to($user->getEmail())
                    ->subject('Porfavor confirma tu Email')
                    ->htmlTemplate('email/register_verification.html.twig')
            );

            $this->addFlash('success', 'Operaci??n realizada, revisa tu email y haz clic en el enlace para completar la operaci??n de registro.');

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }


    // Dar de baja usuario
    #[Route('/unsubscribe}', name: 'unsubscribe', methods: ['GET', 'POST'])]
    public function unsubscribe(Request $request, ManagerRegistry $doctrine, LoggerInterface $appUserInfoLogger, FileService $uploader, RequestStack $requestStack):Response{

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $usuario = $this->getUser(); //recuperamos usuario logeado
        
        $formulario = $this->createForm(UserDeleteFormType::class, $usuario);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $uploader->targeDirectory = $this->getParameter('app.users_pics_root');

            if ($usuario->getFotografia()) {
                $uploader->delete($usuario->getFotografia());
            }

            // $entityManager = $this->getDoctrine()->getManager();
            $entityManager = $doctrine->getManager();
            $entityManager->remove($usuario);
            $entityManager->flush();

            $session = $this->requestStack->getSession();

            // $this->container->get('security.token_storage')->setToken(null);
            // $this->container->get('session')->invalidate();

            // TODO: como cerrar sesi??n!!!
            // $session->get('security.token_storage')->setToken(null);
            // $session->get('session')->invalidate();

            // flashear el mensaje
            $mensaje = 'Usuario '.$usuario->getDisplayname().' eliminado correctamente';
            $this->addFlash('success', $mensaje);
            
            // loguear el mensaje
            $mensaje = 'Usuario '.$usuario->getDisplayname().' se ha dado de baja.';
            $appUserInfoLogger->warning($mensaje);

            // redirigimos a portada
            return $this->redirectToRoute('portada');
        }

        return $this->renderForm("user/delete.html.twig", [
            "formulario" => $formulario,
            "usuario" => $usuario
        ]);

    }


}
