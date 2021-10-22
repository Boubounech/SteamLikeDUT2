<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response; // Attention très important utilisez bien ce paquet sinon vous allez avoir une erreur
use Symfony\Component\Routing\Annotation\Route; // Nouveau use pour les annotations
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Nouveau use pour importer l'abstract controller

class ExempleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     **/
    public function index() {
        return new Response('Hello world !'); // Nous utilisons le paquet Response (obligatoire sinon erreur de symfony)
    }

    /**
     * @Route("/indextwig")
     */
    public function indextwig() {
        return $this->render('index.html.twig', [
            'title' => 'hello world'
        ]);
    }

    /**
     * @Route("/saymyname/{name}", name="random")
     **/
    public function random($name) {
        return new Response('Hello ' . $name . ' !');
    }

    /**
     * @Route("/questions/{slug}")
     */
    public function show($slug)
    {
        $answers = [
            'réponse liongue 1',
            'reponse aussi longue 2',
            'toujours plus 3',
        ];

        return $this->render('question/show.html.twig',[
            'question' => ucwords(str_replace('-',' ',$slug)),
            'answers' => $answers,
        ]);
    }

}