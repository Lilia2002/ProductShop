<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:user:create';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PasswordHasherFactoryInterface */
    private $hasherFactory;

    public function __construct(EntityManagerInterface $entityManager, PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->entityManager = $entityManager;
        $this->hasherFactory = $hasherFactory;

        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $consoleStyle = new SymfonyStyle($input, $output);

        $email = $consoleStyle->ask('Введите email пользователя');

        $user  = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        while ($user) {
            $output->writeln('Пользователь с таким Email уже существует');

            $email = $consoleStyle->ask('Введите email пользователя');

            $user  = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        }

        $firstName = $consoleStyle->ask('Введите имя пользователя');
        $lastName  = $consoleStyle->ask('Введите фамилию пользователя');
        $userName  = $consoleStyle->ask('Введите никнейм пользователя');
        $roles     = $consoleStyle->askQuestion(
            (new ChoiceQuestion(
                'Выберите роль пользователя:(по дефолту 0)',
                ['ROLE_USER', 'ROLE_MANAGER', 'ROLE_ADMIN'],
                0
            ))->setMultiselect(true)
        );

        $password = $consoleStyle->ask('Придумайте пароль пользователя');

        $user = new User();

        $user->setEmail($email);
        $user->setUsername($userName);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles($roles);
        $user->setPassword($password);

        $passwordHash = $this->hasherFactory->getPasswordHasher($user);
        $user->setPassword($passwordHash->hash($user->getPassword()));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Пользователь успешно добавлен!');
        return Command::SUCCESS;
    }
}