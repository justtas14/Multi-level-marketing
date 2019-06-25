<?php


namespace App\Command;

use App\Entity\Associate;
use App\Entity\Invitation;
use App\Entity\User;
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
                'Enter email of the admin to remove associate'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userRepository = $this->em->getRepository(User::class);
        $associateRepository = $this->em->getRepository(Associate::class);

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
        } elseif ($associateRepository->findAssociateChildren($associate->getAncestors().$associate->getId())) {
            $output->writeln('Associate has children!');
        } else {
            $user->setAssociate(null);
            $invitations = $this->em->getRepository(Invitation::class)->findBy(['sender' => $associate]);
            foreach ($invitations as $invitation) {
                $this->em->remove($invitation);
            }
            $this->em->persist($user);
            $this->em->flush();
            $this->em->remove($associate);
            $this->em->flush();
            $output->writeln('Associate successfully removed');
        }
    }
}
