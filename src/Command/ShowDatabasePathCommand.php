<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\DBAL\Connection;

#[AsCommand(
    name: 'app:show-database-path',
    description: 'Shows the current database path used by the application',
)]
class ShowDatabasePathCommand extends Command
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Database Connection Information');

        $params = $this->connection->getParams();
        $driver = $params['driver'] ?? 'unknown';

        if ($driver === 'pdo_sqlite') {
            $path = $params['path'] ?? 'unknown';
            $io->success("Using SQLite database at: $path");

            // Check if file exists and is readable
            if (file_exists($path)) {
                $io->info('Database file exists and is ' . (is_readable($path) ? 'readable' : 'not readable'));
                $io->info('File size: ' . filesize($path) . ' bytes');

                // Try to get table count
                try {
                    $tables = $this->connection->fetchAllAssociative("SELECT name FROM sqlite_master WHERE type='table'");
                    $io->info('Tables in database: ' . count($tables));
                    foreach ($tables as $table) {
                        $io->text(' - ' . $table['name']);
                    }
                } catch (\Exception $e) {
                    $io->error('Could not query database: ' . $e->getMessage());
                }
            } else {
                $io->error('Database file does not exist!');
            }

            $absolutePath = realpath($path) ?: 'Could not resolve path';
            $io->info("Absolute path: $absolutePath");
        } else {
            $host = $params['host'] ?? 'unknown';
            $dbname = $params['dbname'] ?? 'unknown';
            $user = $params['user'] ?? 'unknown';
            $io->success("Using $driver database at $host/$dbname with user $user");
        }

        // Show .env database URL
        $envDbUrl = $_ENV['DATABASE_URL'] ?? 'not found';
        $io->info("DATABASE_URL in environment: $envDbUrl");

        return Command::SUCCESS;
    }
}
