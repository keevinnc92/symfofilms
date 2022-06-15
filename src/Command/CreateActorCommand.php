<?php

namespace App\Command;

use App\Entity\Actor;
use App\Repository\ActorRepository;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:create-actor',
    description: 'Add a short description for your command',
)]
class CreateActorCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    // PROPIEDADES
    private $em;
    private $ActorRepository;

    // CONSTRUCTOR
    public function __construct(EntityManagerInterface $em, ActorRepository $actorRepository){

        parent::__construct();

        $this->em = $em;
        $this->actorRepository = $actorRepository;
    }




    protected function configure(): void
    {
        $this->setDescription('Este comando nos permite crear actores')
             ->setHelp('Los parámetros son nombre, fecha de nacimiento, nacionalidad y biografía')
             ->addArgument('nombre', InputArgument::REQUIRED, 'Nombre:')
             ->addArgument('fecha_nacimiento', InputArgument::REQUIRED, 'FechaNacimiento:')
             ->addArgument('nacionalidad', InputArgument::REQUIRED, 'Nacionalidad:')
             ->addArgument('biografia', InputArgument::OPTIONAL, 'Biografia:')
             ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln('<fg=white;bg=black>Crear actor</>');

        // recupera los datos de los parámetros de consola
        $nombre = $input->getArgument('nombre');
        $fecha_nacimiento = $input->getArgument('fecha_nacimiento');
        $fecha_nacimiento = new \DateTime($fecha_nacimiento);
        $nacionalidad = $input->getArgument('nacionalidad');
        $biografia = $input->getArgument('biografia');

        // crea el actor
        $actor = (new Actor())->setNombre($nombre)->setFechaNacimiento($fecha_nacimiento)->setNacionalidad($nacionalidad)->setBiografia($biografia);

        // guarda el usuario en la BDD
        $this->em->persist($actor);
        $this->em->flush();

        $output->writeln("<fg=white;bg=green>Actor $nombre creado</>");
        return Command::SUCCESS;








        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
