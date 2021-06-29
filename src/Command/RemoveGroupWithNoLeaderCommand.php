<?php

namespace App\Command;

use App\Entity\Rooms;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveGroupWithNoLeaderCommand extends Command
{
    protected static $defaultName = 'app:removeGroupWithNoLeader';
    protected static $defaultDescription = 'Removes all Users which are members of a group thats leader is not user of the room anymore';
    private $em;
    public function __construct(string $name = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->em = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription);

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $rooms = $this->em->getRepository(Rooms::class)->findAll();
        $io->info('Got '.sizeof($rooms).' Events');

        foreach ($rooms as $data){
            $io->info('We are working with the Event: '.$data->getName());
            foreach ($data->getUser() as $data2){
                $io->info('we are doing research on User: '. $data2->getEmail());

                foreach ($data2->getEventGroups() as $group){

                }
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
