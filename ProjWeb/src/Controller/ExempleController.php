<?php

namespace App\Controller;

use App\Service\RandomHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExempleController extends AbstractController
{
    /**
     * @Route("/couscous", name="testhome")
     **/
    public function index(RandomHelper $random)
    {
        return $this->render('base.html.twig', [
          'title' => 'Hello Guys !!',
          'randomStuff' => $random->getStuff()
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

