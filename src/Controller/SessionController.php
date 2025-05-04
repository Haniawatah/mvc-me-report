<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'session_index')]
    public function index(SessionInterface $session): Response
    {
        $data = [];
        foreach ($session->all() as $key => $value) {
            if ($key === '_sf2_attributes') {
                continue;
            }
            
            // Handle object inspection while respecting case-insensitive filenames
            if (is_object($value)) {
                $className = get_class($value);
                if ($key === 'deck' && $value !== null) {
                    $data[$key] = [
                        'type' => $className,
                        'cardCount' => $value->getCount()
                    ];
                    continue;
                }
                
                // Add generic object inspection
                $data[$key] = [
                    'type' => $className,
                    'properties' => $this->extractObjectInfo($value)
                ];
                continue;
            }
            
            $data[$key] = $value;
        }
        
        return $this->render('session/index.html.twig', [
            'session_data' => $data
        ]);
    }

    #[Route('/session/delete', name: 'session_delete')]
    public function delete(SessionInterface $session): Response
    {
        $session->clear();
        
        $this->addFlash('notice', 'Session has been cleared!');
        
        return $this->redirectToRoute('session_index');
    }
    
    /**
     * Extract useful information from session objects
     */
    private function extractObjectInfo(object $object): array
    {
        $info = [];
        
        // Add common methods if they exist
        if (method_exists($object, 'getCount')) {
            $info['count'] = $object->getCount();
        }
        
        // Add more specific object information based on class
        $className = get_class($object);
        if (str_contains($className, 'DeckOfCards')) {
            $info['cardsRemaining'] = $object->getCount();
        } elseif (str_contains($className, 'CardHand')) {
            $info['cardsInHand'] = $object->getCount();
        }
        
        return $info;
    }
}
