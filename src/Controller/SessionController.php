<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/session')]
class SessionController extends AbstractController
{
    #[Route('/', name: 'session_index')]
    public function index(SessionInterface $session): Response
    {
        $data = [];

        foreach ($session as $key => $value) {
            if (is_object($value)) {
                $data[$key] = [
                    'type' => get_class($value)
                ];

                // Add specific details for certain objects
                if ($key === 'deck' && method_exists($value, 'getCount')) {
                    $data[$key]['cardCount'] = $value->getCount();
                }
            } else {
                $data[$key] = $value;
            }
        }

        return $this->render('session/index.html.twig', [
            'session_data' => $data,
        ]);
    }

    #[Route('/delete', name: 'session_delete')]
    public function delete(SessionInterface $session): Response
    {
        $session->clear();

        $this->addFlash('notice', 'Session has been cleared.');

        return $this->redirectToRoute('session_index');
    }
}
