<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\GameCategory;
use App\Entity\UserGame;
use App\Form\GameType;
use App\Repository\CategoryRepository;
use App\Repository\GameCategoryRepository;
use App\Repository\GameRepository;
use App\Repository\UserGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

        $ugRep = $em->getRepository(UserGame::class);
        $downloads = [];
        foreach ($games as $game)
        {
            $downloads[$game->getId()] = count($ugRep->findBy(['idGame' => $game->getId()])); // Retourne le nombre de fois que le jeu a été dl
        }


        return $this->render('index.html.twig', [
          'games' => $games,
          'empty' => $empty,
          'downloads' => $downloads,
        ]);
    }

    /**
     * @Route("/games/new", methods={"GET", "POST"}, name="addGame")
     */
    public function add(Request $request, EntityManagerInterface $em, CategoryRepository $cat, GameRepository $gr)
    {
        if (!$this->isGranted('ROLE_USER'))
            return $this->redirectToRoute('home', []);

        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $game->setIdUser($this->getUser()->getId());
            $categoryId = $game->getCategory();
            $game->setCategory($cat->findOneBy(['id' => $categoryId])->getName());

            $em->persist($game);
            $em->flush();

            $gameCat = new GameCategory();
            $gameCat->setIdCategory($categoryId);
            $gameCat->setIdGame($game->getId());

            $em->persist($gameCat);
            $em->flush();
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('games/new.html.twig', [
          'game' => $game,
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/games", methods={"GET","POST"}, name="gamesList")
     */
    public function list(EntityManagerInterface $em, UserGameRepository $ugRep): Response
    {
        $repository = $em->getRepository(Game::class);
        $games = $repository->findAll();

        $downloads = [];
        foreach ($games as $game)
        {
            $downloads[$game->getId()] = count($ugRep->findBy(['idGame' => $game->getId()])); // Retourne le nombre de fois que le jeu a été dl
        }

        return $this->render('store.html.twig', [
          'games' => $games,
          'downloads' => $downloads,
          ]);
    }

    /**
     * @Route("/games/{param}", methods={"GET"}, name="game_show")
     */
    public function show(EntityManagerInterface $em,$param): Response
    {
        $repository = $em->getRepository(Game::class);

        if ((int)$param)
        {
            $id = $param;
            $game = $repository->findOneBy([ 'id' => $id]);

            if(!$game)
            {
                throw $this->createNotFoundException('Cannot load this game !');
            }

            return $this->render('game.html.twig', [ 'game' => $game]);
        }

        else
        {
            $cRep = $em->getRepository(Category::class);
            if ($cRep->findOneBy(['name' => $param]))
            {
                $category = $param;
                $games = $repository->findBy([ 'category' => $category]);
            }
            else
            {
                $name = $param;
                $games = $repository->findByName($name);
            }




            if(!$games)
            {
                throw $this->createNotFoundException('Cannot load this game !');
            }

            $ugRep = $em->getRepository(UserGame::class);
            $downloads = [];
            foreach ($games as $game)
            {
                $downloads[$game->getId()] = count($ugRep->findBy(['idGame' => $game->getId()])); // Retourne le nombre de fois que le jeu a été dl
            }

            return $this->render('store.html.twig', [
              'games' => $games,
              'downloads' => $downloads,
              ]);
        }



    }

    /**
     * @Route("/games/{id}/edit", methods={"GET", "POST"}, name="game_edit")
     */
    public function edit(Request $request, Game $game, GameRepository $gameRep, EntityManagerInterface $entityManager, int $id, CategoryRepository $cat, GameCategoryRepository $gcRep): Response
    {
        if ($this->getUser()->getId() != $gameRep->findOneBy(['id' => $id])->getIdUser())
            return $this->redirectToRoute('home', []);

        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $gameCat = $gcRep->findOneBy([
              'idGame' => $game->getId(),
            ]);
            $gameCat->setIdCategory($game->getCategory());
            $game->setCategory($cat->findOneBy(['id' => $game->getCategory()])->getName());
            $entityManager->flush();

            return $this->redirectToRoute('users_show', ['id' => $this->getUser()->getId() ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('games/edit.html.twig', [
          'game' => $game,
          'form' => $form,
        ]);
    }

    /**
     * @Route("/games/{id}", methods={"POST"}, name="game_delete")
     */
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager, GameCategoryRepository $gcRep): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $catRep = $entityManager->getRepository(Category::class);
            $gameCat = $gcRep->findOneBy([
              'idCategory' => $catRep->findOneBy(['name' => $game->getCategory()]),
              'idGame' => $game->getId()
              ]);
            $entityManager->remove($gameCat);
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_show', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/games/{id}/download", methods={"GET"}, name="game_download")
     */
    public function download(int $id, EntityManagerInterface $em, UserGameRepository $ugRep, GameRepository $gRep) : Response
    {
        if (!$this->isGranted('ROLE_USER'))
            return $this->redirectToRoute('app_login', []);

        if($ugRep->findOneBy(['idUser' => $this->getUser()->getId(), 'idGame' => $id]) == null)
        {
            $userGame = new UserGame();
            $userGame->setIdGame($id);
            $userGame->setIdUser($this->getUser()->getId());

            $em->persist($userGame);
            $em->flush();
        }

        $game = $gRep->findOneBy(['id' => $id]);
        return $this->redirect($game->getLink());
    }

    /**
     * @Route("/games/search/r", methods={"POST"}, name="redirect_search")
     */
    public function r()
    {
        $search = $_POST['search'];
        return $this->redirectToRoute('game_show', ['param' => $search]);

    }
}
