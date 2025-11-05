<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\DBAL\Connection;

#[AsCommand(
    name: 'app:sync-database',
    description: 'Synchronizes all books across database instances',
)]
class SyncDatabaseCommand extends Command
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
        $io->title('Database Synchronization Tool');

        try {
            // First, check if the books table exists
            $tables = $this->connection->fetchAllAssociative("SELECT name FROM sqlite_master WHERE type='table' AND name='book'");

            if (empty($tables)) {
                $io->error('The book table does not exist! Create it first with: php bin/console doctrine:schema:create');
                return Command::FAILURE;
            }

            // Get all books from the database
            $books = $this->connection->fetchAllAssociative('SELECT * FROM book');
            $io->info('Found ' . count($books) . ' books in the database.');

            // Display all current books
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

            // Ensure the database has the "Clean Code" book
            $cleanCode = $this->connection->fetchAssociative("SELECT * FROM book WHERE isbn = '9780132350884'");
            if (!$cleanCode) {
                $io->info("Adding 'Clean Code' book to database...");
                $this->connection->executeStatement(
                    'INSERT INTO book (title, isbn, author, description) VALUES (?, ?, ?, ?)',
                    [
                        'Clean Code',
                        '9780132350884',
                        'Robert C. Martin',
                        'A handbook of agile software craftsmanship'
                    ]
                );
                $io->success("Added 'Clean Code' book to database.");
            } else {
                $io->info("'Clean Code' book already exists in the database.");
            }

            // Now check if the second book exists by id=2 and show the data
            $secondBook = $this->connection->fetchAssociative('SELECT * FROM book WHERE id = 2');
            if ($secondBook) {
                $io->info('Book with ID=2 details:');
                $io->table(
                    ['Field', 'Value'],
                    [
                        ['id', $secondBook['id']],
                        ['title', $secondBook['title']],
                        ['author', $secondBook['author']],
                        ['isbn', $secondBook['isbn']],
                        ['description', $secondBook['description'] ?? '']
                    ]
                );

                // If the second book has minimal data, update it to be more complete
                if ($secondBook['title'] === 'a' && $secondBook['author'] === 'a') {
                    $io->info('Book with ID=2 has minimal data. Updating...');
                    $this->connection->executeStatement(
                        'UPDATE book SET title = ?, author = ?, description = ? WHERE id = 2',
                        [
                            'Design Patterns',
                            'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                            'Elements of Reusable Object-Oriented Software'
                        ]
                    );
                    $io->success('Updated book with ID=2.');
                }
            } else {
                $io->info('No book with ID=2 found. Adding a new sample book...');
                $this->connection->executeStatement(
                    'INSERT INTO book (title, isbn, author, description) VALUES (?, ?, ?, ?)',
                    [
                        'Design Patterns',
                        '9780201633610',
                        'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                        'Elements of Reusable Object-Oriented Software'
                    ]
                );
                $io->success("Added 'Design Patterns' book to database.");
            }

            // Verify the sync worked by getting all books again
            $books = $this->connection->fetchAllAssociative('SELECT * FROM book');
            $io->success('Database now has ' . count($books) . ' books.');

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

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
