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
                'Congratulations! You have been registered successfully'
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

    /**
     * User edit form. Delete account button and modal.
     */
    public function profileAction()
    {
        $request = $this->getRequest();
        $user = $this->get('security.context')->getToken()->getUser();
        // Form from UserSettingsType class in order to use a different validation
        $form = $this->createForm(new UserSettingsType(), $user);
        // Form to confirm when the user wants to delete its account
        $delete_user_form = $this->createFormBuilder(null)
            ->add('username_confirm', 'text', array(
                'label' => 'Please type in your email to confirm',
                'attr' => array(
                    'id' => 'inputWarning',
                    'class' => 'form-control',
                    'placeholder' => 'Your email here to confirm')
                ))
            ->getForm();

        $oldpassword = $form->getData()->getPassword();
        // New in version 2.3: The handleRequest() method was added in Symfony 2.3. 
        // Previously, the $request was passed to the submit method - a strategy which 
        // is deprecated and will be removed in Symfony 3.0.
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Update user's profile
            $newLocale = $form->getData()->getLocale();

            if (null == $user->getPassword()) 
            {
                // User have not changed the password
                $user->setPassword($oldpassword);
            }
            else 
            {
                $encoder = $this->get('security.encoder_factory')
                                ->getEncoder($user);
                $newpassword = $encoder->encodePassword(
                    $user->getPassword(),
                    $user->getSalt()
                );
                $user->setPassword($newpassword);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
 
            $this->get('session')->getFlashBag()->add('success',
                'Profile data has been updated successfully'
            );
 
            return $this->redirect($this->generateUrl('rov_users_profile', array(
                '_locale' => $newLocale
                )
            ));
        }

        return $this->render('ROVUsersBundle:Default:settings.html.twig', array(
            'user' => $user,
            'settings_form' => $form->createView(),
            'delete_user_form' => $delete_user_form->createView()
        ));
    }

    /**
     * Delete account and everything this user has.
     * This action is not able to be reversed.
     */
    public function deleteAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            
            $user = $this->get('security.context')->getToken()->getUser();
            // *********** WHY????? **************************************/
            foreach($request->request->all() as $req){
                $username_confirm = $req['username_confirm'];
            }

            if ($username_confirm == $user->getEmail()) 
            {
                if (null == $user || !$this->get('security.context')->isGranted('ROLE_USER')) {
                    $this->get('session')->getFlashBag()->add('info',
                        'In order to delete your account, you must sign in first'
                    );

                    return $this->redirect($this->generateUrl('rov_users_login'));
                }

                $em = $this->getDoctrine()->getManager();
                $em->remove($user);
                $em->flush();
                // Automatic logout
                $this->get('request')->getSession()->invalidate();
                $this->get('security.context')->setToken(null);
            } 
            else 
            {
                $this->get('session')->getFlashBag()->add('error',
                    'Email incorrect. Your account has not been deleted.'
                    );

                return $this->redirect($this->generateUrl('rov_users_profile'));
            }

        }

        return $this->redirect($this->generateUrl('home'));
    }
}
