<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\UserGame;
use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * @Route("/user")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="users_index")
     */
    public function index(UsersRepository $usersRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN'))
            return $this->redirectToRoute('home', []);

        return $this->render('users/index.html.twig', [
            'users' => $usersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/signup", methods={"GET", "POST"}, name="app_signup")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, AuthenticationUtils $authenticationUtils): Response
    {
        // Redirect si pas connecté
        if (!$this->isGranted('IS_AUTHENTICATED_ANONYMOUSLY'))
            return $this->redirectToRoute('home', []);

        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
              $passwordEncoder->hashPassword($user, $user->getPassword())
            );
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="users_show")
     */
    public function show(Users $user, int $id, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN'))
            return $this->render('users/show.html.twig', [
              'user' => $user,
            ]);

        if ($this->getUser()->getId() == $id)
        {
            $repository = $em->getRepository(Game::class);
            $games = $repository->findBy(['idUser' => $this->getUser()->getId()]);

            $ugRep = $em->getRepository(UserGame::class);

            $downloads = [];
            foreach ($games as $game)
            {
                $downloads[$game->getId()] = count($ugRep->findBy(['idGame' => $game->getId()])); // Retourne le nombre de fois que le jeu a été dl
            }

            return $this->render('users/profile.html.twig', [
              'user' => $user,
              'games' => $games,
              'downloads' => $downloads,
            ]);
        }


        return $this->redirectToRoute('home', []);
    }

    /**
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="users_edit")
     */
    public function edit(Request $request, Users $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, int $id, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()->getId() != $id and !$this->isGranted('ROLE_SUPER_ADMIN'))
            return $this->redirectToRoute('home', []);

        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
              $passwordEncoder->hashPassword($user, $user->getPassword()));
            $entityManager->flush();

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->renderForm('users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/{id}}", methods={"POST"}, name="users_delete")
     */
    public function delete(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
    }
}
