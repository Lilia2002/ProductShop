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

class EditRolesCommand extends Command
{
    protected static $defaultName = 'app:user:edit-roles';

    /** @var EntityManagerInterface */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $consoleStyle = new SymfonyStyle($input, $output);

        $user = null;
        while (!$user) {
            $email = $consoleStyle->ask('Введите email пользователя, роль которого, Вы бы хотели изменить');
            $user  = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $output->writeln('Пользователь с таким Email не найден');
            }
        }

        $roles = $consoleStyle->askQuestion(
            (new ChoiceQuestion(
                'Выберите роль пользователя:(по дефолту 0), текущие роли:' . implode(',', $user->getRoles()),
                ['ROLE_USER', 'ROLE_MANAGER', 'ROLE_ADMIN'],
                0
            ))->setMultiselect(true)
        );

        $user->setRoles($roles);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Данные успешно изменены!');
        return Command::SUCCESS;
    }
}

