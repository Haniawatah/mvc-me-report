<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:show-books',
    description: 'Displays all books in the database',
)]
class ShowBooksCommand extends Command
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
        $io->title('Stored Books');

        try {
            // First check if the table exists
            $tableExists = false;

            try {
                $tables = $this->connection->fetchAllAssociative("SELECT name FROM sqlite_master WHERE type='table' AND name='book'");
                $tableExists = count($tables) > 0;
            } catch (\Exception $e) {
                $io->warning("Could not check if 'book' table exists: " . $e->getMessage());
                $tableExists = false;
            }

            if (!$tableExists) {
                $io->warning('The book table does not exist yet!');
                $io->text([
                    'To create the database schema:',
                    '1. Run: php bin/console doctrine:schema:create',
                    '2. To add sample books, run: php bin/console dbal:run-sql "INSERT INTO book (title, isbn, author, description) VALUES (\'Clean Code\', \'9780132350884\', \'Robert C. Martin\', \'A handbook of agile software craftsmanship\')"'
                ]);
                return Command::SUCCESS;
            }

            // Get all books directly from the database
            $books = $this->connection->fetchAllAssociative('SELECT * FROM book');

            if (empty($books)) {
                $io->warning('No books found in the database.');
                $io->text([
                    'To add books:',
                    '1. Visit the library at /library',
                    '2. Click "Add New Book" and fill out the form',
                    '3. Or add a book directly with:',
                    '   php bin/console dbal:run-sql "INSERT INTO book (title, isbn, author, description) VALUES (\'Clean Code\', \'9780132350884\', \'Robert C. Martin\', \'A handbook of agile software craftsmanship\')"'
                ]);
                return Command::SUCCESS;
            }

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

            $io->success(sprintf('Found %d books', count($books)));

            // Add info about the database path
            $params = $this->connection->getParams();
            if (isset($params['path'])) {
                $io->info('Database path: ' . $params['path']);
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
