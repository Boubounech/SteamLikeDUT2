<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Game;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\GameType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GameController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/", name="index", methods="GET")
     * @return Response
     */
    public function index(): Response
    {
        $games = $this->getDoctrine()->getRepository(Game::class)->findAll();
        return $this->render("index.html.twig", [
            "games" => $games
        ]);
    }

    /**
     * @Route("/game/{id}", name="game", methods="GET")
     * @return Response
     */
    public function select(Game $game, Request $request): Response
    {
        $comment = new Comment();
        if ($this->security->isGranted("IS_AUTHENTICATED_REMEMBERED")) {
            $user = $this->getUser();
            $comment->setOwner($user);
        }
        $comment->setGame($game);
        $form = $this->createForm(CommentType::class, $comment)->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("game", ["id" => $game->getId()]);
        }
        $user = $game->getOwner();
        $comments = $game->getComments();
        $connectedUser = $this->getUser();
        return $this->render("game/game.html.twig", [
            "game" => $game,
            "user" => $user,
            "comments" => $comments,
            "connectedUser" => $connectedUser,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/games", name="allGames", methods="GET")
     * @return Response
     */
    public function allGamesPaginated(): Response
    {
        $games = $this->getDoctrine()->getRepository(Game::class)->findAll();
        return $this->render("game/allGames.html.twig", [
            "games" => $games
        ]);
    }

    /**
     * @Route("/games/create", name="CreateGame", methods="POST")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function createGame(Request $request) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $game = new Game();

        $game->setOwner($this->getUser());
        $game->setDlnumber(0);

        $form = $this->createForm(GameType::class, $game)->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($game);
            $em->flush();
            return $this->redirectToRoute("game", ["id" => $game->getId()]);
        }

        return $this->render("game/createGame.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/game/{id}/modify", name="ChangeGameInfos", methods="PUT")
     */
    public function changeGame(Game $game, Request $request) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(GameType::class, $game)->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($game);
            $em->flush();
            return $this->redirectToRoute("game", ["id" => $game->getId()]);
        }

        return $this->render("game/changeGame.html.twig", [
            "form" => $form->createView(),
            "game" => $game
        ]);
    }

    /**
     * @Route("/game/{id}/delete", name="DeleteGame", methods="DELETE")
     */
    public function deleteGame(Game $game) : Response
    {
        $em = $this->getDoctrine()->getManager();

        if($game != null) {
            $em->remove($game);
            $em->flush();
        }

        return $this->redirectToRoute("index");
    }

    /**
     * @Route ("/game/{id}/redirect", name="RedirectToGameLink")
     * @param Game $game
     * @return Response
     */
    public function redirectToDownload(Game $game) : Response
    {
        $em = $this->getDoctrine()->getManager();
        //$game->setDlnumber($game->getDlnumber() + 1);
        $downloaders = $game->getDownloaders();
        foreach ($downloaders as $downloader) {
            if ($downloader == $this->getUser()) {
                return $this->redirect($game->getLink());
            }
        }

        $game->addDownloader($this->getUser());
        $em->persist($game);
        $em->flush();
        return $this->redirect($game->getLink());
    }


    /**
     * @Route("/search", name="SearchGames", methods="GET")
     */
    public function researchGames(Request $request) : Response
    {
        $tit = $request->query->get('tit');
        $cate = $request->query->get('cate');
        $games = $this->getDoctrine()->getRepository(Game::class)->findByNameAndCategory($tit, $cate);
        return $this->render("game/searchGames.html.twig", [
            "games" => $games,
            "title" => $tit,
            "category" => $cate
        ]);
    }

    /**
     * @Route ("/game/{id}/like", name="LikeGame")
     */
    public function addLike(Game $game) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $likers = $game->getLikers();
        foreach ($likers as $liker) {
            if ($liker == $this->getUser()) {
                return $this->redirectToRoute("game", ["id" => $game->getId()]);
            }
        }

        $game->addLiker($this->getUser());
        $em->persist($game);
        $em->flush();
        return $this->redirectToRoute("game", ["id" => $game->getId()]);
    }
}