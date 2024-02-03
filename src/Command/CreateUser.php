<?php

namespace App\Command;

use App\Model\OAuthClient;
use App\Model\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'spices:create-user',
    description: 'Creates a new user.',
)]
class CreateUser extends Command
{
    private string $oauthClient = 'spiceStore';

    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher) {
        $this->userPasswordHasher = $userPasswordHasher;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'User name')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $oAuthClient = OAuthClient::where('name', $this->oauthClient)->first();

        if (empty($oAuthClient)) {
            $io->error('Application does not have oAuth client yet! Please create client on the oAuth server with the name \'' . $this->oauthClient . '\'.');

            return Command::FAILURE;
        }

        $user = User::where('login', $input->getArgument('username'))->first();

        if (!empty ($user)) {
            $io->error('User with username \'' . $input->getArgument('username') . '\' already exists.');

            return Command::FAILURE;
        }

        $user = new User();
        $user->login = $input->getArgument('username');
        $user->password = $this->userPasswordHasher->hashPassword($user, $input->getArgument('password'));
        $user->client_id = $oAuthClient->identifier;

        $user->save();

        $io->success('User ' . $input->getArgument('username') . ' created successfully.');

        return Command::SUCCESS;
    }
}