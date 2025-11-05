<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/library')]
class LibraryApiController extends AbstractController
{
    #[Route('/books', name: 'api_library_books', methods: ['GET'])]
    public function getAllBooks(BookRepository $bookRepository): JsonResponse
    {
        try {
            $books = $bookRepository->findAll();

            $data = [];
            foreach ($books as $book) {
                $data[] = [
                    'id' => $book->getId(),
                    'title' => $book->getTitle(),
                    'isbn' => $book->getIsbn(),
                    'author' => $book->getAuthor(),
                    'image' => $book->getImage(),
                    'description' => $book->getDescription()
                ];
            }

            return $this->json($data);
        } catch (\Exception $e) {
            // Return a fallback response with an empty array if DB connection fails
            return $this->json(
                ['error' => 'Database connection error', 'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/book/{isbn}', name: 'api_library_book', methods: ['GET'])]
    public function getBookByIsbn(string $isbn, BookRepository $bookRepository): JsonResponse
    {
        try {
            $book = $bookRepository->findOneByIsbn($isbn);

            if (!$book) {
                return $this->json(['error' => 'Book not found'], Response::HTTP_NOT_FOUND);
            }

            $data = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'isbn' => $book->getIsbn(),
                'author' => $book->getAuthor(),
                'image' => $book->getImage(),
                'description' => $book->getDescription()
            ];

            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Database error', 'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
