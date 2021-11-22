<?php

namespace App\Controller;

#use App\Src\App;
#use function mysql_xdevapi\getSession;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SteamController extends AbstractController
{
    /**
     * @Route("/home/{name}")
     */
    public function homeHandler($name)
    {
        return new Response("Home " . $name);
        #$games = $this->app->getService('steamModel')->getLastGames();
        #$this->render('MainView', ["games" => $games]);
    }

    public function gamesHandler()
    {
        session_start();
        $games = $this->app->getService('steamModel')->getAllGames();
        $this->render('magasin', ["games" => $games]);
    }

    public function gameHandler($name)
    {
        $game = $this->app->getService('steamModel')->getOneGame($name);
        $this->render('jeu', $game);
    }

    public function gamesSearchByName()
    {
        $name = $_POST['name'];
        $games = $this->app->getService('steamModel')->searchByName($name);
        $this->render('magasin', ["games" => $games]);
    }

    public function gamesSearchByCategory($category)
    {
        $games = $this->app->getService('steamModel')->searchByCategory($category);
        $this->render('magasin', ["games" => $games]);
    }

    public function renderProfile()
    {
        session_start();
        $games = $this->app->getService('steamModel')->getDownloadedGames($_SESSION['id']);
        $this->render('profile', ['games' => $games]);
    }

    public function actionOnGame($name, $action)
    {
        if ($action === "modifier")
        {
            $this->renderModify($name);
        }
        if ($action === "supprimer")
        {
            $this->renderDelete($name);
        }
    }

    private function renderModify($name)
    {
        $game = $this->app->getService('steamModel')->getOneGame(htmlspecialchars($name));
        $this->render('modifier', htmlspecialchars($game));
    }

    public function modifyGame()
    {
        $id = $_POST['id'];
        $newName = $_POST['name'];
        $newDesc = $_POST['desc'];
        $newCategory = $_POST['category'];
        $newLink = $_POST['link'];

        $this->app->getService('steamModel')->modifyGame($id, htmlspecialchars($newName), htmlspecialchars($newDesc), htmlspecialchars($newCategory), htmlspecialchars($newLink));
        $this->renderProfile();
    }

    private function renderDelete($name)
    {
        $game = $this->app->getService('steamModel')->getOneGame($name);
        $this->render('supprimer', htmlspecialchars($game));
    }

    public function deleteGame($name)
    {
        $this->app->getService('steamModel')->deleteGame(htmlspecialchars($name), $_SESSION['id']);
        $this->homeHandler();
    }

    public function renderPostePage()
    {
        $this->render('poster');
    }

    public function posteGame()
    {
        session_start();
        $name = $_POST['name'];
        $desc = $_POST['desc'];
        $category = $_POST['category'];
        $link = $_POST['link'];
        $creatorID = $_SESSION['id'];
        $this->app->getService('steamModel')->uploadGame(htmlspecialchars($name), htmlspecialchars($desc), htmlspecialchars($category), htmlspecialchars($link), $creatorID);
        $this->gamesHandler();
    }

    public function renderConnection()
    {
        $this->render('connection');
    }
    public function renderInscription()
    {
        $this->render('inscription');
    }
    public function validationInscription()
    {
        $user = $this->app->getService('steamModel')->validationInscription(htmlspecialchars($_POST["email"]), htmlspecialchars($_POST["mdp"]), htmlspecialchars($_POST["pseudo"]));
        $this->homeHandler();
    }
    public function validationConnection()
    {
        $user = $this->app->getService('steamModel')->validationConnection(htmlspecialchars($_POST["email"]), htmlspecialchars($_POST["mdp"]));
        $this->homeHandler();
    }
    public function downloadGame()
    {
        session_start();

        $user = $this->app->getService('steamModel')->downloadGame($_SESSION['id'], $_POST['idjeu']);
        $this->redirectToDlLink($_POST['dllink']);
    }
    public function redirectToDlLink($link) {
        header('Location:'.$link);
    }

    public function modifyProfile()
    {
        session_start();
        $idUser = htmlspecialchars($_SESSION['id']);
        $pseudo = htmlspecialchars($_POST['pseudo']);
        $mail = htmlspecialchars($_POST['mail']);
        $this->app->getService('steamModel')->modifyProfile($idUser, $pseudo, $mail);
        $this->renderProfile();
    }
    public function render404() {
        $this->render('404');
    }
}
