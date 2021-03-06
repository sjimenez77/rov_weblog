<?php

namespace ROV\StartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * Homepage
     * @param  Request $request [description]
     * @return [object] Twig template
     */
    public function homeAction(Request $request)
    {
        $session = $request->getSession();
    	  $em = $this->getDoctrine()->getManager();

        // Get authentication utils
        $helper = $this->get('security.authentication_utils');

        // Get the login error if there is any
        $error = $helper->getLastAuthenticationError();

        $lastArticles = $em->getRepository('ROVBlogBundle:Article')->findBy(
        	array('published' => true),
        	array('updated' => 'DESC'),
        	4,
        	0
        );

        $lastWineReviews = $em->getRepository('ROVBlogBundle:Wine')->findBy(
            array('published' => true),
            array('updated' => 'DESC'),
            5,
            0
        );

        $defaultData = array();
        $formSearch = $this->createFormBuilder($defaultData)
                ->add('search', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type your search'
                    )
                ))
                ->getForm();

        return $this->render('ROVStartBundle:Default:home.html.twig', array(
        	'articles'          => $lastArticles,
            'wines'             => $lastWineReviews,
            'page'              => 'home',
            'form_search'       => $formSearch->createView(),
            'form_search_wine'  => $formSearch->createView(),
            'last_username'     => $helper->getLastUsername(),
            'error'             => $error
        ));
    }

    /**
     * Contact form
     * @param  Request $request [description]
     * @return [object] Twig template
     */
    public function contactAction(Request $request)
    {
        $session = $request->getSession();

        // Get authentication utils
        $helper = $this->get('security.authentication_utils');

        // Get the login error if there is any
        $error = $helper->getLastAuthenticationError();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER'))
        {
            // Load user name and surname into the form
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $contact_form = $this->createFormBuilder()
                ->add('Name', 'text', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'readonly' => 'readonly',
                        'value' => $user->getName()
                        )
                    ))
                ->add('Surname', 'text', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'readonly' => 'readonly',
                        'value' => $user->getSurname()
                        )
                    ))
                ->add('Email', 'email', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'readonly' => 'readonly',
                        'value' => $user->getUsername()
                        )
                    ))
                ->add('Subject', 'text', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Type the subject'
                        )
                    ))
                ->add('Message', 'textarea', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'rows'  => '5'
                        )
                    ))
            ->getForm();
        }
        elseif ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_ANONYMOUSLY'))
        {
            $contact_form = $this->createFormBuilder()
                ->add('Name', 'text', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Type your name'
                        )
                    ))
                ->add('Surname', 'text', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Type your surname'
                        )
                    ))
                ->add('Email', 'email', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Type your email'
                        )
                    ))
                ->add('Subject', 'text', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Type the subject'
                        )
                    ))
                ->add('Message', 'textarea', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'rows'  => '5'
                        )
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
                    ->setSubject('Contacto '.$this->container->getParameter('rov_global_title').': '.$data['Subject'])
                    ->setFrom(array(
                        $this->container->getParameter('mailer_user') => $this->container->getParameter('rov_global_title')
                        )
                    )
                    ->setTo($this->container->getParameter('rov_admin_email'))
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
                'last_username' => $helper->getLastUsername(),
                'error'         => $error
            )
        );
    }
}
