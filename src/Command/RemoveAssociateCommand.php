<?php


namespace App\Command;

use App\Entity\Associate;
use App\Entity\Invitation;
use App\Entity\User;
use App\Service\AssociateManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveAssociateCommand extends Command
{
    protected static $defaultName = 'app:remove:associate';

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /** @var AssociateManager $associateManager */
    private $associateManager;

    public function __construct(EntityManagerInterface $entityManager, AssociateManager $associateManager)
    {
        $this->em = $entityManager;
        $this->associateManager = $associateManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'Enter email of the admin to remove associate'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userRepository = $this->em->getRepository(User::class);

        $enteredEmail = $input->getArgument('email');

        /** @var User $user */
        $user = $userRepository->findOneBy(['email' => $enteredEmail]);
        if ($user) {
            $associate = $user->getAssociate();
        }

        if (!$user) {
            $output->writeln('User not found');
        } elseif (!$user->getAssociate()) {
            $output->writeln('Associate does not exist');
        } elseif (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $output->writeln('User is not admin!');
        } else {
            $isDeleted = $this->associateManager->deleteAssociate($associate);
            if (!$isDeleted) {
                $output->writeln('Associate has children!');
            } else {
                $output->writeln('Associate successfully removed');
            }
        }
    }
}
