<?php

declare(strict_types=1);

namespace App\Command;

use App\Bus\Bus;
use App\Bus\Command\CreateUserCommand as BusCreateAdminCommand;
use App\Repository\UserRepository;
use App\Service\RandomStringGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates a new admin | app:create-admin <name> <email>',
    hidden: false,
)]
class CreateAdminCommand extends Command
{
    private ValidatorInterface $validator;
    private Bus $bus;
    private RandomStringGenerator $randomStringGenerator;
    private UserRepository $userRepository;

    public function __construct(
        ValidatorInterface $validator,
        Bus $bus,
        RandomStringGenerator $randomStringGenerator,
        UserRepository $userRepository,
    ) {
        parent::__construct();

        $this->validator = $validator;
        $this->bus = $bus;
        $this->randomStringGenerator = $randomStringGenerator;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'User name')
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Admin Creator',
            '============',
            '',
        ]);

        $email = $input->getArgument('email');
        $emailConstraint = new Assert\Email([
            'message' => 'The email {{ value }} is not valid.',
        ]);
        $violations = $this->validator->validate($email, $emailConstraint);

        if (null !== $this->userRepository->findOneByEmail($email)) {
            $output->writeln("<error>Admin with email \"$email\" already exists.</error>");

            return Command::FAILURE;
        }

        if (0 === count($violations)) {
            $name = $input->getArgument('name');
            $password = $this->randomStringGenerator->generate(5);

            $this->bus->command(new BusCreateAdminCommand(
                name: $name,
                email: $email,
                updatedPassword: $password,
                roles: ['ROLE_ADMIN'],
            ));
            $output->writeln("<fg=green>Admin $name<$email> created! Password:$password</>");

            return Command::SUCCESS;
        }

        foreach ($violations as $violation) {
            $output->writeln('<error>'.$violation->getMessage().'</error>');
        }

        return Command::FAILURE;
    }
}
