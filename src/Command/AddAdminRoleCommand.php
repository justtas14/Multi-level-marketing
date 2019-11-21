<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddAdminRoleCommand extends Command
{
    protected static $defaultName = 'app:add:role_admin';

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
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'Enter email of the user to add admin role'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userRepository = $this->em->getRepository(User::class);

        $enteredEmail = $input->getArgument('email');


        /** @var User $user */
        $user = $userRepository->findOneBy(['email' => $enteredEmail]);

        if (!$user) {
            $output->writeln('User not found');
        } else {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $output->writeln('User is already admin!');
            } else {
                $user->addRole('ROLE_ADMIN');
                $this->em->persist($user);
                $this->em->flush();
                $output->writeln('Admin role successfully added');
            }
        }
    }
}
