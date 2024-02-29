<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use App\Service\UtilsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }
    #[Route('/enigma', name: 'app_register_adm')]
    public function register(Request $request, UserPasswordHasherInterface $hash): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $user->setEmail(UtilsService::cleanInput($user->getEmail()));

            if (!$this->userRepository->findOneBy(["email" => $user->getEmail()])) {

                $user->setPassword($hash->hashPassword($user, UtilsService::cleanInput($user->getPassword())));
                $user->setRoles(["ROLE_USER", "ROLE_ADMIN"]);
                $this->em->persist($user);
                $this->em->flush();
                $notice = "success";
                $msg = "Le compte admin : " . $user->getEmail() . " a été ajouté";
            } else {

                $notice = "danger";
                $msg = "Le compte admin : " . $user->getEmail() . " existe déja";
            }

            $this->addFlash($notice, $msg);
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
