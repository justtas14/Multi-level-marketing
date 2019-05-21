<?php


namespace App\Command;


use App\Entity\Associate;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create:admin';

    /**
     * @var EntityManager $em
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
            // ...
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $associate = new Associate();
        $user = new User();

        $user->setEmail($input->getArgument('email'));
        $user->setPlainPassword($input->getArgument('password'));
        $associate->setFullName($input->getArgument('fullName'));
        $associate->setEmail($input->getArgument('email'));
        $user->setAssociate($associate);
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $this->em->persist($associate);
        $this->em->flush();
        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Data successfully loaded');
    }
}