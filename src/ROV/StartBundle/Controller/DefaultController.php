<?php

namespace ROV\StartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    /**
     * Homepage
     * @return [object] Twig template
     */
    public function homeAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
    	$em = $this->getDoctrine()->getManager();

        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $lastArticles = $em->getRepository('ROVBlogBundle:Article')->findBy(
        	array(),
        	array('updated' => 'DESC'),
        	5,
        	0
        );

        // View some variables with ladyBug
        $this->get('ladybug')->log($lastArticles);
        $this->get('ladybug')->log($request);
        $this->get('ladybug')->log($session);

        return $this->render('ROVStartBundle:Default:home.html.twig', array(
        	'articles'      => $lastArticles,
            'page'          => 'home',
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error
        ));
    }

    /**
     * Contact form
     * @return [object] Twig template
     */
    public function contactAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            // Admin go home!!
            return $this->redirect($this->generateUrl('rov_start_homepage'));
        }
        elseif ($this->get('security.context')->isGranted('ROLE_USER')) 
        {
            // Load user name and surname into the form
            $user = $this->get('security.context')->getToken()->getUser();
            $contact_form = $this->createFormBuilder()
                ->add('Name', 'text', array(
                    'attr' => array(
                        'class' => 'span8',
                        'readonly' => 'readonly',
                        'value' => $user->getName()
                        )
                    ))
                ->add('Surname', 'text', array(
                    'attr' => array(
                        'class' => 'span8',
                        'readonly' => 'readonly',
                        'value' => $user->getSurname()
                        )
                    ))
                ->add('Email', 'email', array(
                    'attr' => array(
                        'class' => 'span8',
                        'readonly' => 'readonly',
                        'value' => $user->getUsername()
                        )
                    ))
                ->add('Subject', 'text', array(
                    'attr' => array('class' => 'span8')
                    ))
                ->add('Message', 'textarea', array(
                    'attr' => array('class' => 'span8')
                    ))
            ->getForm();
        } 
        elseif ($this->get('security.context')->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')) 
        {
            $contact_form = $this->createFormBuilder()
                ->add('Name', 'text', array(
                    'attr' => array('class' => 'span8')
                    ))
                ->add('Surname', 'text', array(
                    'attr' => array('class' => 'span8')
                    ))
                ->add('Email', 'email', array(
                    'attr' => array('class' => 'span8')
                    ))
                ->add('Subject', 'text', array(
                    'attr' => array('class' => 'span8')
                    ))
                ->add('Message', 'textarea', array(
                    'attr' => array('class' => 'span8')
                    ))
            ->getForm();
        }

        if ($request->getMethod() == 'POST') {

            $contact_form->bind($request);
            
            if ($contact_form->isValid()) {
                // Obtain data from the contact form and send the message
                $data = $contact_form->getData();

                $message = \Swift_Message::newInstance()
                    ->setContentType('text/html')
                    ->setCharset('UTF-8')
                    ->setSubject($data['Subject'])
                    ->setFrom(array(
                        $data['Email'] => $data['Name'].' '.$data['Surname']
                        )
                    )
                    ->setTo($this->container->getParameter('rainbow_admin_email'))
                    ->setBody(
                        $this->renderView(
                            '::email_contact.html.twig',
                            array(
                                'name' => $data['Name'],
                                'surname' => $data['Surname'],
                                'subject' => $data['Subject'],
                                'email' => $data['Email'],
                                'message' => $data['Message']
                                )
                            )
                        )
                    ;

                $this->get('mailer')->send($message);

                $this->get('session')->getFlashBag()->add('success',
                    'Your message has been sent successfully'
                );
            }
        }

        return $this->render('ROVStartBundle:Default:contact.html.twig', 
            array(
                'page' => 'contact',
                'contact_form' => $contact_form->createView(),
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error
            )
        );
    }
}
