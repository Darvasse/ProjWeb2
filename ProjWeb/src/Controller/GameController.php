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
    /**
     * @Route("/", methods={"GET","HEAD"}, name="home")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Game::class);

        $games = $repository->findLastGames();
        $empty = False;
        if (!$games)
            $empty = True;


        return $this->render('index.html.twig', ['games' => $games, 'empty' => $empty]);
    }

    /**
     * @Route("/games", methods={"POST"}, name="addGame")
     */
    public function add(EntityManagerInterface $em)
    {
        $game = new Game();
        $game->setName("Valorant")
            ->setCategory("FPS")
            ->setDescription("Pew pew !");

        $em->persist($game);
        $em->flush();

        return new Response("Added successfuly");
    }

    /**
     * @Route("/games", methods={"GET","HEAD"}, name="gamesList")
     */
    public function list(EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Game::class);

        $games = $repository->findAll();

        $empty = False;
        if (!$games)
            $empty = True;

        return $this->render('store.html.twig', ['games' => $games, 'empty' => $empty]);
    }

    /**
     * @Route("/games/{name}", methods={"GET","HEAD"}, name="oneGame")
     */
    public function getOne(EntityManagerInterface $em, string $name): Response
    {
        $repository = $em->getRepository(Game::class);

        $game = $repository->findOneBy([ 'name' => $name]);

        if(!$game)
        {
            throw $this->createNotFoundException('Cannot load this game !');
        }

        return $this->render('game.html.twig', [ 'game' => $game]);
    }
}
