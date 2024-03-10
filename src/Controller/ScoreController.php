<?php

namespace App\Controller;

use App\Entity\Score;
use App\Form\ScoreType;
use App\Repository\FlagRepository;
use App\Repository\ParticipantRepository;
use App\Repository\ScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ScoreController extends AbstractController
{
    private FlagRepository $flagRepository;
    private ParticipantRepository $participantRepository;
    private ScoreRepository $scoreRepository;
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;

    public function __construct(
        FlagRepository $flagRepository,
        ScoreRepository $scoreRepository,
        ParticipantRepository $participantRepository,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
    ) {
        $this->flagRepository = $flagRepository;

        $this->scoreRepository = $scoreRepository;

        $this->participantRepository = $participantRepository;

        $this->em = $em;

        $this->serializer = $serializer;
    }

    #[Route('/score', name: 'app_score')]
    public function index(Request $request): Response
    {

        $score = new Score();
        $form = $this->createForm(ScoreType::class, $score);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            //récupération du flag
            $flag = $this->flagRepository->findOneBy(["label" => $request->request->all('score')["Flag"]]);
            //test si le flag existe
            if ($flag) {
                //récupération du score de l'utilisateur
                $scorePart = $this->scoreRepository->findOneBy(["participant" =>
                $this->participantRepository->find($score->getParticipant()->getId())]);

                //test si l'utilisateur à un score
                if ($scorePart) {
                    //state si le participant n'a pas le flag
                    $state = true;
                    //parcour des flags du participant
                    foreach ($scorePart->getFlags() as $value) {
                        //test si le user à déja le flag
                        if ($value->getLabel() == $flag->getLabel()) {
                            //dd("déja le flag");
                            $notice = "warning";
                            $msg = "Vous avez déja le flag";
                            //le participant à déja le flag
                            $state = false;
                        }
                    }
                    //test si le participant n'a pas le flag
                    if($state) {
                        //ajoute le flag au participant
                        $scorePart->addFlag($flag);
                        //ajout du flag en BDD
                        $this->em->persist($scorePart);
                        $this->em->flush();
                        $notice = "success";
                        $msg = "Vous avez gagné : " . $flag->getValeur() . " pts";
                    } 
                }
                //le participant n'a pas encore de score
                else {
                    //ajoute un score à l'utilisateur
                    $score->setDateScore(new \DateTimeImmutable("now"));
                    //ajoute le flag à l'utilisateur
                    $score->addFlag($flag);
                    //ajoute en bdd le score (avec le flag)
                    $this->em->persist($score);
                    $this->em->flush();
                    $notice = "success";
                    $msg = "Vous venez de gagner : " . $flag->getValeur() . " pts";
                }
            } 
            //test le flag n'existe pas
            else {
                $notice = "danger";
                $msg = "le flag n'existe pas";
            }
            $this->addFlash($notice, $msg);
        }
        return $this->render('score/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/score/all', name:'app_score_all')]
    public function showScore() : Response 
    {
        $scores = $this->scoreRepository->findAll();
        
        return $this->render('score/stat_score.html.twig', [
            'scores' => $scores,
        ]);
    }

    
    #[Route('/api/score', name: 'app_score_api', methods:'POST')]
    public function apiScore(Request $request): Response
    {
        //recupération du json
        $json = $request->getContent();
        //conexion du json
        $data = $this->serializer->decode($json, 'json');
        //récupération du flag
        $flag = $this->flagRepository->findOneBy(["label" => $data["flag"]]);
        //test si le flag existe
        if ($flag) {
            //récupération du score de l'utilisateur
            $scorePart = $this->scoreRepository->findOneBy(["participant" =>
            $this->participantRepository->findOneBy(["nom"=>$data["nom"]])]);
            
            //test si l'utilisateur à un score
            if ($scorePart) {
                //state si le participant n'a pas le flag
                $state = true;
                //parcour des flags du participant
                foreach ($scorePart->getFlags() as $value) {
                    //test si le user à déja le flag
                    if ($value->getLabel() == $flag->getLabel()) {
                        //dd("déja le flag");
                        $notice = 200;
                        $msg = "Vous avez déja le flag";
                        //le participant à déja le flag
                        $state = false;
                    }
                }
                //test si le participant n'a pas le flag
                if($state) {
                    //ajoute le flag au participant
                    $scorePart->addFlag($flag);
                    //ajout du flag en BDD
                    $this->em->persist($scorePart);
                    $this->em->flush();
                    $notice = 200;
                    $msg = "Vous avez gagné : " . $flag->getValeur() . " pts";
                } 
            }
            //le participant n'a pas encore de score
            else {
                $score = new Score();
                $score->setParticipant($this->participantRepository->findOneBy(["nom"=>$data["nom"]]));
                //ajoute un score à l'utilisateur
                $score->setDateScore(new \DateTimeImmutable("now"));
                //ajoute le flag à l'utilisateur
                $score->addFlag($flag);
                //ajoute en bdd le score (avec le flag)
                $this->em->persist($score);
                $this->em->flush();
                $notice = 200;
                $msg = "Vous venez de gagner : " . $flag->getValeur() . " pts";
            }
        } 
        //test le flag n'existe pas
        else {
            $notice = 400;
            $msg = "le flag n'existe pas";
        }
        return $this->json(["msg" => $msg], $notice, 
        ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);   
    }
}
