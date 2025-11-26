<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for project mini-site under /proj.
 */
class ProjController
{
    #[Route('/proj', name: 'proj_index')]
    public function index(Request $request): Response
    {
        ob_start();
        $baseVar = $request->getBasePath(); // e.g. /~maix24/dbwebb-kurser/mvc/me/report/public
        include __DIR__ . '/../../proj/index.php';
        return new Response(ob_get_clean());
    }

    #[Route('/proj/about', name: 'proj_about')]
    public function about(Request $request): Response
    {
        ob_start();
        $baseVar = $request->getBasePath();
        include __DIR__ . '/../../proj/about.php';
        return new Response(ob_get_clean());
    }
}
