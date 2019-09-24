<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login()
    {


        return $this->render('auth/login.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }



    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {


       
    }

    /**
     * @Route("/register", name="register")
     */
    public function auth(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager)
    {


        $user = new User();


        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $plainPassword = $user->getPassword();
            $hash = $encoder->encodePassword($user, $plainPassword);

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'barvo, vous pouvez maintenant vous connecter');

            return $this->redirectToRoute('login');

        }


        return $this->render('auth/register.html.twig', [
            'form' => $form->createView()
        ]);
     }


}
