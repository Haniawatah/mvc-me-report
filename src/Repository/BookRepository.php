<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Exception; // import Exception

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
        $this->entityManager = $registry->getManager();
    }

    /**
     * Save a book entity to the database
     *
     * @param Book $entity The book to save
     * @param bool $flush Whether to flush the entity manager
     * @return void
     * @throws Exception If there's an error saving the book
     */
    public function save(Book $entity, bool $flush = false): void
    {
        try {
            $this->entityManager->persist($entity);

            if ($flush) {
                $this->entityManager->flush();
            }
        } catch (Exception $e) {
            throw new Exception('Could not save book: ' . $e->getMessage());
        }
    }

    /**
     * Remove a book entity from the database
     *
     * @param Book $entity The book to remove
     * @param bool $flush Whether to flush the entity manager
     * @return void
     * @throws Exception If there's an error removing the book
     */
    public function remove(Book $entity, bool $flush = false): void
    {
        try {
            $this->entityManager->remove($entity);

            if ($flush) {
                $this->entityManager->flush();
            }
        } catch (Exception $e) {
            throw new Exception('Could not remove book: ' . $e->getMessage());
        }
    }

    /**
     * Find all books in the database
     *
     * @return array<Book>
     * @throws Exception If there's an error retrieving the books
     */
    public function findAll(): array
    {
        try {
            return parent::findAll();
        } catch (Exception $e) {
            throw new Exception('Database connection error: ' . $e->getMessage());
        }
    }

    /**
     * Find a book by its ID
     *
     * @param int $id The book ID
     * @return Book|null The book entity or null if not found
     * @throws Exception If there's an error retrieving the book
     */
    public function find($id, $lockMode = null, $lockVersion = null): ?Book
    {
        try {
            return parent::find($id, $lockMode, $lockVersion);
        } catch (Exception $e) {
            throw new Exception('Database connection error: ' . $e->getMessage());
        }
    }

    /**
     * Find a book by its ISBN
     *
     * @param string $isbn The book ISBN
     * @return Book|null The book entity or null if not found
     * @throws Exception If there's an error retrieving the book
     */
    public function findOneByIsbn(string $isbn): ?Book
    {
        try {
            return $this->createQueryBuilder('b')
                ->andWhere('b.isbn = :isbn')
                ->setParameter('isbn', $isbn)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (Exception $e) {
            throw new Exception('Database connection error: ' . $e->getMessage());
        }
    }

    /**
     * Debug the database connection and structure
     *
     * @return array Information about the database connection
     */
    public function debugDatabase(): array
    {
        try {
            $conn = $this->getEntityManager()->getConnection();
            $platform = $conn->getDatabasePlatform()->getName();

            // Get database information safely without calling getName() on the driver
            $params = $conn->getParams();
            $driverName = $params['driver'] ?? 'unknown';

            // For SQLite, check if tables exist
            if (str_contains($driverName, 'sqlite')) {
                $tables = $conn->fetchAllAssociative("SELECT name FROM sqlite_master WHERE type='table'");

                $result = [
                    'connection' => 'successful',
                    'driver' => $driverName,
                    'platform' => $platform,
                    'path' => $params['path'] ?? 'unknown',
                    'tables' => array_map(fn ($table) => $table['name'], $tables),
                    'book_count' => count($this->findAll())
                ];
            } else {
                // For MySQL/MariaDB or other databases
                $result = [
                    'connection' => 'successful',
                    'driver' => $driverName,
                    'platform' => $platform,
                    'database' => $params['dbname'] ?? 'unknown',
                    'host' => $params['host'] ?? 'unknown',
                    'user' => $params['user'] ?? 'unknown',
                    'book_count' => count($this->findAll())
                ];
            }

            return $result;
        } catch (Exception $e) {
            return [
                'connection' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }
}
