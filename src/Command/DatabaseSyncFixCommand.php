<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\DBAL\Connection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use PDO;

#[AsCommand(
    name: 'app:fix-database',
    description: 'Fixes database synchronization issues between web and console',
)]
class DatabaseSyncFixCommand extends Command
{
    private Connection $connection;
    private string $projectDir;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->projectDir = dirname(__DIR__, 2); // Get project root directory

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Database Synchronization Fix');

        // 1. Check for database file in config path
        $configDbPath = $this->connection->getParams()['path'] ?? null;
        $io->info('Configured database path: ' . $configDbPath);

        // 2. Check for database files in the project
        $finder = new Finder();
        $finder->files()
            ->in($this->projectDir)
            ->name('*.db')
            ->name('*.sqlite')
            ->name('*.sqlite3');

        $dbFiles = [];
        $webDbPath = null;

        foreach ($finder as $file) {
            $relativePath = str_replace($this->projectDir, '', $file->getRealPath());
            $size = $file->getSize();

            // If this is a non-empty database file that's different from our config path
            if ($size > 0 && $file->getRealPath() !== $configDbPath) {
                $dbFiles[$relativePath] = $file->getRealPath();

                // Database in the public folder is likely the one used by web interface
                if (strpos($relativePath, '/public/') !== false) {
                    $webDbPath = $file->getRealPath();
                }
            }
        }

        if (empty($dbFiles)) {
            $io->info('No other database files found');
        } else {
            $io->info('Found additional database files:');
            foreach ($dbFiles as $path => $fullPath) {
                $io->text(' - ' . $path . ' (' . $this->formatSize(filesize($fullPath)) . ')');
            }
        }

        // 3. Create var directory if it doesn't exist
        $varDir = $this->projectDir . '/var';
        if (!is_dir($varDir)) {
            $io->info('Creating var directory');
            mkdir($varDir, 0777, true);
        }

        // 4. Ensure the configured database file exists
        $targetDbPath = $this->projectDir . '/var/data.db';
        if (!file_exists($targetDbPath)) {
            $io->info("Creating empty database file at {$targetDbPath}");
            file_put_contents($targetDbPath, '');
            chmod($targetDbPath, 0666);
        } else {
            $io->info("Target database already exists at {$targetDbPath}");
        }

        // 5. Make a backup of the current database
        $backupPath = $targetDbPath . '.backup.' . date('YmdHis');
        copy($targetDbPath, $backupPath);
        $io->info("Created backup of current database at {$backupPath}");

        // 6. Option to create the schema
        $io->section('Database Schema');
        $createSchema = $io->confirm('Do you want to create the database schema?', false);

        if ($createSchema) {
            // Use the Symfony command runner to create the schema
            $io->info('Creating database schema...');
            $command = 'php ' . $this->projectDir . '/bin/console doctrine:schema:create --force';
            $process = new Process(explode(' ', $command));
            $process->setWorkingDirectory($this->projectDir);
            $process->run();

            if ($process->isSuccessful()) {
                $io->success('Schema created successfully!');
            } else {
                $io->error('Schema creation failed: ' . $process->getErrorOutput());
            }
        }

        // 7. Option to copy data from another database
        if (!empty($dbFiles) && $webDbPath) {
            $io->section('Data Migration');
            $copyData = $io->confirm('Do you want to copy data from the web database to the console database?', true);

            if ($copyData) {
                $io->info("Copying data from {$webDbPath} to {$targetDbPath}");

                try {
                    // Create a new connection to the web database
                    $webDbConn = new PDO('sqlite:' . $webDbPath);

                    // Get all tables
                    $tables = $webDbConn->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(\PDO::FETCH_COLUMN);

                    foreach ($tables as $table) {
                        $io->info("Processing table: {$table}");

                        // Get all rows from web database
                        $rows = $webDbConn->query("SELECT * FROM {$table}")->fetchAll(\PDO::FETCH_ASSOC);

                        if (empty($rows)) {
                            $io->info("No data in table {$table}");
                            continue;
                        }

                        // Insert into target database
                        $this->connection->executeStatement("DELETE FROM {$table}");

                        foreach ($rows as $row) {
                            $columns = implode(', ', array_keys($row));
                            $placeholders = implode(', ', array_fill(0, count($row), '?'));

                            $this->connection->executeStatement(
                                "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})",
                                array_values($row)
                            );
                        }

                        $io->success('Copied ' . count($rows) . " rows to table {$table}");
                    }

                    $io->success('Data migration completed successfully');
                } catch (\Exception $e) {
                    $io->error('Data migration failed: ' . $e->getMessage());
                }
            }
        }

        // 8. Test database access
        $io->section('Database Test');
        try {
            // Test database connection and book count
            $bookCount = $this->connection->fetchOne('SELECT COUNT(*) FROM book');
            $io->success("Connection successful! Found {$bookCount} books in the database.");

            // Show the books
            $books = $this->connection->fetchAllAssociative('SELECT * FROM book');
            $rows = [];
            foreach ($books as $book) {
                $rows[] = [
                    $book['id'],
                    $book['title'],
                    $book['author'],
                    $book['isbn'],
                    substr($book['description'] ?? '', 0, 50)
                ];
            }

            $io->table(
                ['ID', 'Title', 'Author', 'ISBN', 'Description'],
                $rows
            );
        } catch (\Exception $e) {
            $io->error('Database test failed: ' . $e->getMessage());
        }

        // 9. Update environment variables
        $envPath = $this->projectDir . '/.env.local';
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            $updated = false;

            // Check if DATABASE_URL is already set to the correct value
            if (!preg_match('/DATABASE_URL="sqlite:\/\/%kernel\.project_dir%\/var\/data\.db"/', $envContent)) {
                // Replace the DATABASE_URL line or add it if it doesn't exist
                if (preg_match('/DATABASE_URL=.*/', $envContent)) {
                    $envContent = preg_replace('/DATABASE_URL=.*/', 'DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"', $envContent);
                } else {
                    $envContent .= "\nDATABASE_URL=\"sqlite:///%kernel.project_dir%/var/data.db\"\n";
                }
                $updated = true;
            }

            if ($updated) {
                file_put_contents($envPath, $envContent);
                $io->success('Updated .env.local with correct DATABASE_URL');
            } else {
                $io->info('.env.local already has the correct DATABASE_URL');
            }
        }

        $io->success('Database synchronization fix completed! Your web and console should now use the same database.');

        return Command::SUCCESS;
    }

    private function formatSize(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }
}
