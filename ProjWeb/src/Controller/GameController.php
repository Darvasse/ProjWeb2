<?php

namespace App\Controller;

use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'game')]
    public function index(): Response
    { 
        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
        ]);
    }

    /**
     * @Route("/games/add/", name="addGame")
     */
    public function  add(EntityManagerInterface $em)
    {
        $game = new Game();
        $game->setName("Fifa")
            ->setCategory("Sport")
            ->setDescription("Mmmmm grass!");

        $em->persist($game);
        $em->flush();

        return new Response("Added successfuly");
    }

    /**
     * @Route("/games", name="gamesList")
     */
    public function list(EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Game::class);

        $games = $repository->findAll();

        if(!$games) {
            throw $this->createNotFoundException('Sorry, no game found in database !');
        }

        return $this->render('index.html.twig', ['games' => $games]);
    }

}
