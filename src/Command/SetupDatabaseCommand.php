<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:setup-database',
    description: 'Creates the database schema and adds sample data',
)]
class SetupDatabaseCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $connection = $this->entityManager->getConnection();

            // Create the book table if it doesn't exist
            $connection->executeStatement('
                CREATE TABLE IF NOT EXISTS book (
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    title VARCHAR(255) NOT NULL,
                    isbn VARCHAR(255) NOT NULL,
                    author VARCHAR(255) NOT NULL,
                    image VARCHAR(1000) DEFAULT NULL,
                    description VARCHAR(2000) DEFAULT NULL
                )
            ');

            $io->success('Database schema created successfully.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Database setup failed: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
