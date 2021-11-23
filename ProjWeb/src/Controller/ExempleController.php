<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExempleController extends AbstractController
{
    /**
     * @Route("/", name="testhome")
     **/
    public function index()
    {
        return $this->render('base.html.twig', [
          'title' => 'Hello Guys !!'
        ]);
    }

    /**
     * @Route("/api/{text}", name="api")
     */
    public function api($text)
    {
        $data = [
          'text' => $text
        ];
        return new JsonResponse($data);
    }
}

