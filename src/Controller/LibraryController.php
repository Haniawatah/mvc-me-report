<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\DBAL\Connection;

#[Route('/library')]
class LibraryController extends AbstractController
{
    #[Route('/', name: 'library_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        try {
            $books = $bookRepository->findAll();
            $errorMessage = null;

            // Don't call debugDatabase directly in the main index method
            // to avoid errors affecting the main page
            $dbInfo = [
                'connection' => 'active',
                'book_count' => count($books)
            ];
        } catch (\Exception $e) {
            // If there's a database error, just show an empty list
            $books = [];
            $errorMessage = 'Could not connect to database. Some features may be limited.';
            $dbInfo = ['connection' => 'failed', 'error' => $e->getMessage()];

            // More specific error message for development environment
            if ($this->getParameter('kernel.environment') === 'dev') {
                $errorMessage .= ' Error: ' . $e->getMessage();
            }
        }

        return $this->render('library/index.html.twig', [
            'books' => $books,
            'error_message' => $errorMessage,
            'db_info' => $dbInfo
        ]);
    }

    #[Route('/new', name: 'library_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BookRepository $bookRepository, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $book = new Book();
            $book->setTitle($request->request->get('title', ''));
            $book->setIsbn($request->request->get('isbn', ''));
            $book->setAuthor($request->request->get('author', ''));
            $book->setImage($request->request->get('image', ''));
            $book->setDescription($request->request->get('description', ''));

            try {
                $bookRepository->save($book, true);
                $this->addFlash('success', 'Book has been added!');

                // Store book data in session for debugging
                $session->set('last_added_book', [
                    'id' => $book->getId(),
                    'title' => $book->getTitle(),
                    'author' => $book->getAuthor(),
                    'isbn' => $book->getIsbn()
                ]);

                return $this->redirectToRoute('library_show', ['id' => $book->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Could not save book: ' . $e->getMessage());
            }
        }

        return $this->render('library/new.html.twig');
    }

    #[Route('/{id}', name: 'library_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, BookRepository $bookRepository): Response
    {
        try {
            $book = $bookRepository->find($id);

            if (!$book) {
                throw $this->createNotFoundException('Book not found');
            }

            return $this->render('library/show.html.twig', [
                'book' => $book,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Could not find book: ' . $e->getMessage());
            return $this->redirectToRoute('library_index');
        }
    }

    #[Route('/{id}/edit', name: 'library_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, BookRepository $bookRepository): Response
    {
        try {
            $book = $bookRepository->find($id);

            if (!$book) {
                throw $this->createNotFoundException('Book not found');
            }

            if ($request->isMethod('POST')) {
                $book->setTitle($request->request->get('title', ''));
                $book->setIsbn($request->request->get('isbn', ''));
                $book->setAuthor($request->request->get('author', ''));
                $book->setImage($request->request->get('image', ''));
                $book->setDescription($request->request->get('description', ''));

                $bookRepository->save($book, true);

                $this->addFlash('success', 'Book has been updated!');

                return $this->redirectToRoute('library_show', ['id' => $book->getId()]);
            }

            return $this->render('library/edit.html.twig', [
                'book' => $book,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error editing book: ' . $e->getMessage());
            return $this->redirectToRoute('library_index');
        }
    }

    #[Route('/{id}/delete', name: 'library_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, BookRepository $bookRepository): Response
    {
        try {
            $book = $bookRepository->find($id);

            if (!$book) {
                throw $this->createNotFoundException('Book not found');
            }

            // Simple token validation - just check if it exists
            if ($request->request->has('_token')) {
                $bookRepository->remove($book, true);
                $this->addFlash('success', 'Book has been deleted!');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error deleting book: ' . $e->getMessage());
        }

        return $this->redirectToRoute('library_index');
    }

    #[Route('/debug', name: 'library_debug', methods: ['GET'])]
    public function debug(BookRepository $bookRepository, SessionInterface $session): Response
    {
        try {
            $dbInfo = $bookRepository->debugDatabase();
            $lastAddedBook = $session->get('last_added_book', null);
        } catch (\Exception $e) {
            $dbInfo = [
                'connection' => 'failed',
                'error' => $e->getMessage()
            ];
            $lastAddedBook = null;
        }

        return $this->render('library/debug.html.twig', [
            'db_info' => $dbInfo,
            'last_added_book' => $lastAddedBook
        ]);
    }

    #[Route('/reset', name: 'library_reset', methods: ['GET'])]
    public function reset(Connection $connection, BookRepository $bookRepository): Response
    {
        // Clear all existing books
        try {
            $connection->executeStatement('DELETE FROM book');

            // Add sample books
            $sampleBooks = [
                [
                    'title' => 'Clean Code',
                    'isbn' => '9780132350884',
                    'author' => 'Robert C. Martin',
                    'image' => 'https://m.media-amazon.com/images/I/41xShlnTZTL._SX376_BO1,204,203,200_.jpg',
                    'description' => 'A handbook of agile software craftsmanship'
                ],
                [
                    'title' => 'Design Patterns',
                    'isbn' => '9780201633610',
                    'author' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                    'image' => 'https://m.media-amazon.com/images/I/51szD9HC9pL._SX395_BO1,204,203,200_.jpg',
                    'description' => 'Elements of Reusable Object-Oriented Software'
                ],
                [
                    'title' => 'The Pragmatic Programmer',
                    'isbn' => '9780201616224',
                    'author' => 'Andrew Hunt, David Thomas',
                    'image' => 'https://m.media-amazon.com/images/I/41HXiIojloL._SX396_BO1,204,203,200_.jpg',
                    'description' => 'Your journey to mastery'
                ]
            ];

            foreach ($sampleBooks as $bookData) {
                $book = new Book();
                $book->setTitle($bookData['title']);
                $book->setIsbn($bookData['isbn']);
                $book->setAuthor($bookData['author']);
                $book->setImage($bookData['image']);
                $book->setDescription($bookData['description']);

                $bookRepository->save($book, true);
            }

            $this->addFlash('success', 'Library reset successfully with sample books!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error resetting library: ' . $e->getMessage());
        }

        return $this->redirectToRoute('library_index');
    }
}
