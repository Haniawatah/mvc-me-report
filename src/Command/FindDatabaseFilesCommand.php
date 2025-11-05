<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'app:find-database-files',
    description: 'Finds all SQLite database files in the project',
)]
class FindDatabaseFilesCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Database Files Finder');

        $projectDir = dirname(__DIR__, 2); // Get project root directory
        $io->info("Searching for .db files in: {$projectDir}");

        // Find all .db files in the project directory
        $finder = new Finder();
        $finder->files()
            ->in($projectDir)
            ->name('*.db')
            ->name('*.sqlite')
            ->name('*.sqlite3');

        if (!$finder->hasResults()) {
            $io->warning('No database files found!');
            return Command::SUCCESS;
        }

        $dbFiles = [];
        foreach ($finder as $file) {
            $relativePath = str_replace($projectDir, '', $file->getRealPath());
            $size = $file->getSize();
            $perms = substr(sprintf('%o', fileperms($file->getRealPath())), -4);

            $dbFiles[] = [
                'path' => $relativePath,
                'size' => $this->formatSize($size),
                'last_modified' => date('Y-m-d H:i:s', $file->getMTime()),
                'permissions' => $perms,
                'readable' => is_readable($file->getRealPath()) ? 'Yes' : 'No',
                'writable' => is_writable($file->getRealPath()) ? 'Yes' : 'No'
            ];
        }

        // Show the found database files in a table
        $io->table(
            ['Path', 'Size', 'Last Modified', 'Permissions', 'Readable', 'Writable'],
            $dbFiles
        );

        // Now let's check environment variables for database paths
        $io->section('Environment Variables');
        $io->info('DATABASE_URL: ' . $_ENV['DATABASE_URL'] ?? 'Not set');

        // Check the doctrine configuration
        $io->section('Doctrine Configuration');
        if (file_exists($projectDir . '/config/packages/doctrine.yaml')) {
            $io->success('Doctrine configuration file exists');
        } else {
            $io->warning('Doctrine configuration file not found');
        }

        return Command::SUCCESS;
    }

    private function formatSize(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }
}
