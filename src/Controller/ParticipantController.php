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


class ParticipantController extends AbstractController
{
    private ParticipantRepository $participantRepository;
    private EntityManagerInterface $em;

    public function __construct(ParticipantRepository $participantRepository, EntityManagerInterface $em) 
    {
        $this->participantRepository = $participantRepository;

        $this->em = $em;
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
}
