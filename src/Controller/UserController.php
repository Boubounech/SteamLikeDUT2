<?php


namespace App\Controller;


use App\Entity\Game;
use App\Entity\User;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
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
     * @Route("/my-profile", name="app_profile", methods="GET")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function profile(): Response
    {
        $games = $this->getDoctrine()->getManager()->getRepository(Game::class)
            ->findBy(array("owner" => $this->getUser()->getId()));
        return $this->render("user/profile.html.twig", [
            "games" => $games
        ]);
    }

    /**
     * @Route("/changeInfos", name="ChangeInfos", methods="PUT")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @return Response
     */
    public function changeInfos(Request $request): Response
    {
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($this->getUser()->getId());

        $form = $this->createForm(UserType::class, $user)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("index");
        }
        return $this->render("user/changeInfos.html.twig", [
            "form" => $form->createView()
        ]);
    }
}