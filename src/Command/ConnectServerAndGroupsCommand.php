<?php

namespace App\Command;

use App\Entity\KeycloakGroupsToStandorts;
use App\Entity\Standort;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConnectServerAndGroupsCommand extends Command
{
    protected static $defaultName = 'app:connectServerAndGroups';
    private $em;
    public function __construct(string $name = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->em = $entityManager;

    }

    protected function configure()
    {
        $this
            ->setDescription('This connects a kecloak Group or a emaildomain with a server. Please add the server-Id, which can be found in the database and the keycloakgroup (on windows machines you need  two leading /all --> //all) or the domain of an email (info@example.com --> example.com)')
            ->addArgument('serverId', InputArgument::REQUIRED, 'This is the Server Id to connect to the keycloak Group')
            ->addArgument('keycloakGroup', InputArgument::REQUIRED, 'This is the keycloak Group or email domain. Alle members of this group can use the server to create Rooms');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $serverId = $input->getArgument('serverId');
        $keycloakGroup = $input->getArgument('keycloakGroup');
        $standort = null;
        $standort = $this->em->getRepository(Standort::class)->find($serverId);
        if (!$standort) {
            $io->error('This server is not available.');
            return Command::FAILURE;
        }
        $groupServer = $this->em->getRepository(KeycloakGroupsToStandorts::class)->findOneBy(array('standort'=>$standort,'keycloakGroup'=>$keycloakGroup));

        if ($groupServer){
            $io->error('This Server is already connected to this group');
            return Command::FAILURE;
        }
        $groupServer = new KeycloakGroupsToStandorts();
        $groupServer->setStandort($standort);
        $groupServer->setKeycloakGroup($keycloakGroup);
        $this->em->persist($groupServer);
        $this->em->flush();



        $io->success('We added the group '.$keycloakGroup.' to the server '.$standort->getName());

        return Command::SUCCESS;
    }
}
