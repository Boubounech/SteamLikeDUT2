<?php


namespace App\Controller;


use App\Entity\Game;
use App\Entity\User;
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

}