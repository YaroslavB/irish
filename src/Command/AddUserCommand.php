<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class AddUserCommand extends Command
{
    protected static $defaultName = 'app:add-user';
    protected static $defaultDescription = 'Add user to database ';
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $password_hasher;
    private UserRepository $userRepository;

    /**
     * AddUserCommand constructor.
     */
    public function __construct(
        string $name = null,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $password_hasher,
        UserRepository $userRepository
    ) {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->password_hasher = $password_hasher;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption(
                'email',
                'u',
                InputArgument::REQUIRED,
                'User email'
            )
            ->addOption(
                'password',
                'p',
                InputArgument::REQUIRED,
                'User password'
            )
            ->addArgument(
                'isAdmin',
                InputArgument::OPTIONAL,
                'if this is set user will be Admin',
                0
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);
        $stopwatch = new Stopwatch();
        $stopwatch->start('create-new-user');
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $isAdmin = $input->getArgument('isAdmin');


        $io->title('Please add new user:');
        $io->text(['Add user email and password']);

        if ( ! $email) {
            $email = $io->ask('Enter user email');
        }
        if ( ! $password) {
            $password = $io->askHidden('Password (will be hidden)');
        }
        if ( ! $isAdmin) {
            $question = new Question('isAdmin? 0 or 1 ', 0);
            $isAdmin = $io->askQuestion($question);
        }

        $isAdmin = (bool)$isAdmin;
        try {
            $user = $this->createUser($email, $password, $isAdmin);
        } catch (RuntimeException $error) {
            $io->comment($error->getMessage());

            return Command::FAILURE;
        }


        $success = sprintf(
            '%s was successfully created:  %s',
            $isAdmin ? 'Admin' : 'User',
            $email
        );

        $io->success($success);
        $eventData = $stopwatch->stop('create-new-user');

        $stopwatchMassage = sprintf(
            'New  user id %s . Elapsed time: %.2f ms/memory %.2f MB',
            $user->getId(),
            $eventData->getDuration(),
            $eventData->getMemory() / 1000 / 1000
        );
        $io->comment($stopwatchMassage);


//        if ($email) {
//            $io->note(sprintf('You passed an argument: %s', $email));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }
//
//        $io->success(
//            'You have a new command! Now make it your own! Pass --help to see your options.'
//        );

        return Command::SUCCESS;
    }


    /**
     * Save user to DB
     *
     * @param  string  $email
     * @param  string  $password
     * @param  bool    $isAdmin
     *
     * @return User
     */

    private function createUser(
        string $email,
        string $password,
        bool $isAdmin
    ): User {
        $existingUser = $this->userRepository->findBy(['email' => $email]);
        if ($existingUser) {
            throw new RuntimeException('User exist with this email');
        }
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER']);
        $encodedPass = $this->password_hasher->hashPassword($user, $password);
        $user->setPassword($encodedPass);
        $user->setIsVerified(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
