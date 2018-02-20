<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Member;
use AppBundle\Form\Type\MemberType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class RegistrationController extends Controller{

    /**
     * @Route("/register", name="registration")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function registerAction(Request $request){
        $member = new Member();

        $form = $this->createForm(MemberType::class, $member,[

        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
           $password = $this
               ->get('security.password_encoder')
               ->encodePassword(
                   $member,
                   $member->getPlainpassword()
               )
           ;

           $member->setPassword($password);

           $em = $this->getDoctrine()->getManager();

           $em->persist($member);
           $em->flush();

           $token =new UsernamePasswordToken(
               $member,
               $password,
               'main',
               $member->getRoles()
           );

           $this->get('security.token_storage')->setToken($token);
           $this->get('session')->set('_security_main', serialize($token));

           $this->addFlash('success','You are now registered!');

           return $this->redirectToRoute('homepage');

        }
        return $this->render('registration/register.html.twig',[
            'registartion_form' => $form->createView(),

        ]);
    }
}