<?php


namespace App\Controller;


use App\Entity\Game;
use App\Entity\User;
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
     * @Route("/", name="index")
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
     * @Route("/api/me", name="api_me")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function apiMe()
    {
        return $this->json($this->getUser(), 200, [], [
            'groups' => ['user:read']
        ]);
    }

    /**
     * @Route("/game-{id}", name="game")
     * @return Response
     */
    public function select(Game $game, Request $request): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($game->getOwner());
        $connectedUser = $this->getUser();
        return $this->render("game/game.html.twig", [
            "game" => $game,
            "user" => $user,
            "connectedUser" => $connectedUser
        ]);
    }

    /**
     * @Route("/page", name="allGames")
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
     * @Route("/createGame", name="CreateGame")
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
     * @Route("/changeGame-{id}", name="ChangeGameInfos")
     */
    public function changeGameName(Game $game, Request $request) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(GameType::class, $game)->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($game);
            $em->flush();
            return $this->redirectToRoute("OneGame", ["id" => $game->getId()]);
        }

        return $this->render("changeGame.html.twig", [
            "form" => $form->createView()
        ]);
    }

}