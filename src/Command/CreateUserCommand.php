<?php

namespace App\Command;

use App\Entity\Associate;
use App\Entity\User;
use App\Service\CreateAdmin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create:admin';

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'Password of the user.')
            ->addArgument('fullName', InputArgument::REQUIRED, 'Full name of the user')
            ->addArgument('mobilePhone', InputArgument::REQUIRED, 'Mobile phone of the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adminService = new CreateAdmin($this->em);
        $adminService->createAdmin(
            $input->getArgument('email'),
            $input->getArgument('password'),
            $input->getArgument('fullName'),
            $input->getArgument('mobilePhone')
        );

        $output->writeln('Data successfully loaded');
    }
}
