<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\UtilsService;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class ParticipantController extends AbstractController
{
    private ParticipantRepository $participantRepository;
    private EntityManagerInterface $em;

    private SerializerInterface $serializer;

    public function __construct(ParticipantRepository $participantRepository, EntityManagerInterface $em, SerializerInterface $serializer) 
    {
        $this->participantRepository = $participantRepository;

        $this->em = $em;

        $this->serializer = $serializer;
    }

    #[Route('/participant/add', name: 'app_participant_add')]
    public function addParticipant(Request $request): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class,$participant);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()) {
            $participant->setNom(UtilsService::cleanInput($participant->getNom()));
            $participant->setPrenom(UtilsService::cleanInput($participant->getPrenom()));
            $participant->setLogin(substr($participant->getPrenom(),0,1) . $participant->getNom());
            $participant->setActive(true);
            if($this->participantRepository->findOneBy(["nom"=>$participant->getNom(), "prenom"
            => $participant->getPrenom()])) {
                $notice = "danger";
                $msg = "Vous étes déja inscrit, votre login est : " . $participant->getLogin();
            }
            else {
                $this->em->persist($participant);
                $this->em->flush();
                $notice = "success";
                $msg = "Inscription réussi, votre login est : " . $participant->getLogin();
            }
            $this->addFlash($notice, $msg);
        }
        
        return $this->render('participant/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/participant/add', name: 'app_participant_add_api', methods:'POST')]
    public function addParticipantApi(Request $request) : Response 
    {
        $msg = [];
        $code = 200;
        $json = $request->getContent();
        //test si le json est valide
        if($request){
            //conversion
            $data = $this->serializer->decode($json, 'json');
            //vérification des données
            if($data["nom"]!="" and $data["prenom"]!="") {
                //récup du compte
                $part = $this->participantRepository->findOneBy(["nom"=>$data["nom"], "prenom"=>$data["prenom"]]);
                if($part) {
                    $msg = "Vous étes déja inscrit avec le login : " . $part->getLogin();
                }
                else {
                    
                    $participant = new Participant();
                    $participant->setNom(UtilsService::cleanInput($data["nom"]));
                    $participant->setPrenom(UtilsService::cleanInput($data["prenom"]));
                    $participant->setLogin(substr($participant->getPrenom(),0,1) . $participant->getNom());
                    $participant->setActive(true);
                    $this->em->persist($participant);
                    $this->em->flush();
                    $msg = "Inscription réussi, votre login est : " . $participant->getLogin();
                }
            }
        }
        return $this->json(["message"=>$msg],$code, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }
}
