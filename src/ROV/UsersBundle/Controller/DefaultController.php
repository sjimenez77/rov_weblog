<?php

namespace ROV\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;

use ROV\UsersBundle\Entity\User;
use ROV\UsersBundle\Form\Frontend\UserSettingsType;
use ROV\UsersBundle\Form\Frontend\UserType;

class DefaultController extends Controller
{
    /**
     * Login form
     * @param  Request $request [description]
     * @return [object] Twig template
     */
    public function loginAction(Request $request)
    {
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
     * @param  Request $request [description]
     * @return [object] Twig template
     */
    public function registerAction(Request $request)
    {
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
     * @param  Request $request [description]
     * @return [object] Twig template
     */
    public function profileAction(Request $request)
    {
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
     * @return [object] Twig template
     */
    public function deleteAction(Request $request)
    {
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

    /**
     * Shows the form to obtain a new password and sends an email with the new generated one
     * @param  Request $request [description]
     * @return [object] Twig template
     */
    public function passwordAction(Request $request)
    {
        $session = $request->getSession();
        // get the login error if there is one
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $user = $this->get('security.context')->getToken()->getUser();

        $defaultData = array();
        $newPasswordForm = $this->createFormBuilder($defaultData)
                ->add('email', 'email', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type your email'
                    )
                ))
                ->getForm();

        if ($this->get('security.context')->isGranted('ROLE_USER')) 
        {
            // User granted can change the password in the profile page
            $this->get('session')->getFlashBag()->add('error',
                'You can change here your password'
            );
            
            return $this->redirect($this->generateUrl('rov_users_profile'));
        }
        else
        {
            $newPasswordForm->handleRequest($request);
            if ($newPasswordForm->isValid())
            {
                $data = $newPasswordForm->getData();
                // Check if the user exists
                $em = $this->getDoctrine()->getManager();
                $userToFind = $em->getRepository('ROVUsersBundle:User')->findOneBy(array('email' => $data['email']));

                if ($userToFind)
                {
                    // New random password
                    $newPasswordString = substr(md5(uniqid($data['email'])), 0, 8);
                    // Encode the new password
                    $encoder = $this->get('security.encoder_factory')
                                    ->getEncoder($userToFind);
                    $newpassword = $encoder->encodePassword(
                        $newPasswordString,
                        $userToFind->getSalt()
                    );
                    // Set the new encoded password
                    $userToFind->setPassword($newpassword);
                    // Persist 
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($userToFind);
                    $em->flush();

                    // Send the new password
                    $message = \Swift_Message::newInstance()
                        ->setContentType('text/html')
                        ->setCharset('UTF-8')
                        ->setSubject($this->get('translator')->trans('New password'))
                        ->setFrom(array(
                            $this->container->getParameter('mailer_user') => $this->container->getParameter('rov_global_title')
                            )
                        )
                        ->setTo($data['email'])
                        ->setBody(
                            $this->renderView(
                                '::email_contact.html.twig',
                                array(
                                    'name' => $userToFind->getName(),
                                    'surname' => $userToFind->getSurname(),
                                    'subject' => $this->get('translator')->trans('New password'),
                                    'email' => $userToFind->getEmail(),
                                    'newpassword' => true,
                                    'message' => $this->get('translator')->trans('Your new password is').' '.$newPasswordString
                                    )
                                )
                            )
                        ;

                    $this->get('mailer')->send($message);

                    $this->get('session')->getFlashBag()->add('success',
                        'Check your mailbox and sign in with the new password'
                    );
                    
                    return $this->redirect($this->generateUrl('rov_users_login'));
                }
                else
                {
                    // User does not exist
                    $this->get('session')->getFlashBag()->add('error',
                        'Email address unknown'
                    );

                    return $this->render('ROVUsersBundle:Default:getPassword.html.twig', array(
                        'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                        'error'         => $error,
                        'new_password_form' => $newPasswordForm->createView(),
                    ));                
                }
            }
            else
            {
                // Show the form
                return $this->render('ROVUsersBundle:Default:getPassword.html.twig', array(
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error'         => $error,
                    'new_password_form' => $newPasswordForm->createView(),
                ));                
            }
        }
    }

    /**
     * Lists the registered users
     * @param  Request $request [description]
     * @return [object] Twig template
     */
    public function userListAction(Request $request)
    {
        $session = $request->getSession();
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $users = $em->getRepository('ROVUsersBundle:User')->findBy(array(), array('registerDate'=>'desc'));;

        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
        {
            // Show the form
            return $this->render('ROVUsersBundle:Default:usersList.html.twig', array(
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                'users'         => $users,
            ));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('error',
                'You do not have enough privileges to access to the users list'
            );

            return $this->redirect($this->generateUrl('rov_blog_homepage'));
        }
    }
}
