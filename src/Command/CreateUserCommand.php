<?php 

// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    // PROPIEDADES
    private $em;
    private $UserRepository;
    private $userPasswordHasher;

    // CONSTRUCTOR
    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository){

        parent::__construct();

        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    // método para indicar la configuración del comando
    protected function configure(): void{
        $this->setDescription('Este comando nos permite crear usuarios')
             ->setHelp('Los parámetros son email, nombre, password y verified')
             ->addArgument('email', InputArgument::REQUIRED, 'Email:')
             ->addArgument('displayname', InputArgument::REQUIRED, 'Nombre para mostrar:')
             ->addArgument('password', InputArgument::REQUIRED, 'Password:')
             ->addArgument('is_verified', InputArgument::OPTIONAL, 'Verified:');
    }  

    // proceso de ejecución del comando
    protected function execute(InputInterface $input, OutputInterface $output): int{
        $output->writeln('<fg=white;bg=black>Crear usuario</>');

        // recupera los datos de los parámetros de consola
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $displayname = $input->getArgument('displayname');
        $is_verified = $input->getArgument('is_verified') ? 1:0;


        if ($this->userRepository->findOneBy(['email' => $email])) {
            $output->writeln("<error> El usuario con email $email ya ha sido registrado anteriormente</error>");
            return Command::FAILURE;
        }

        // crea el usuario
        $user = (new User())->setEmail($email)->setDisplayname($displayname);
        
        // encripta el password y lo asigna
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setIsVerified($is_verified);

        // guarda el usuario en la BDD
        $this->em->persist($user);
        $this->em->flush();

        $output->writeln("<fg=white;bg=green>Usuario $email creado</>");
        return Command::SUCCESS;

    }

}

