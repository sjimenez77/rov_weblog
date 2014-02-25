<?php

namespace ROV\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use ROV\UsersBundle\Entity\User;
use ROV\UsersBundle\Form\Frontend\UserSettingsType;
use ROV\UsersBundle\Form\Frontend\UserType;

class DefaultController extends Controller
{
    public function profileAction()
    {
        return $this->render('ROVUsersBundle:Default:profile.html.twig');
    }

    /**
     * Login form
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        return $this->render('ROVUsersBundle:Default:login.html.twig', 
            array(
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error
            )
        );
    }

    /**
     * Register form and action. Automatic login for a new user.
     */
    public function registerAction()
    {
        $request = $this->getRequest();

        $user = new User();
        $form = $this->createForm(new UserType(), $user);

        // New in version 2.3: The handleRequest() method was added in Symfony 2.3. 
        // Previously, the $request was passed to the submit method - a strategy which 
        // is deprecated and will be removed in Symfony 3.0.
        $form->handleRequest($request);
            
        if ($form->isValid()) {
            // Complete the fields the user does not fill in the form 
            // and store the data in database
            $encoder = $this->get('security.encoder_factory')
                            ->getEncoder($user);
            
            $user->setSalt(md5(time()));
            
            $passwordCiphered = $encoder->encodePassword(
                $user->getPassword(),
                $user->getSalt()
            );

            $user->setPassword($passwordCiphered);

            $user->setRoles('ROLE_USER');
 
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
 
            $this->get('session')->getFlashBag()->add('success',
                'Congratulations! You have been registered in Rainbow Cloud successfully'
            );

            // Automatic login for new user
            $token = new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                'user',
                $user->getRoles()
            );

            $this->container->get('security.context')->setToken($token);

            return $this->redirect($this->generateUrl('home'));
        }

        $session = $request->getSession();

        // get the login error if there is one
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        if( $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
            // authenticated FULLY
            return $this->redirect($this->generateUrl('home'));
        }   

        return $this->render('ROVUsersBundle:Default:register.html.twig', array(
            'sign_up_form'  => $form->createView(),
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error
            )
        );
    }
}
