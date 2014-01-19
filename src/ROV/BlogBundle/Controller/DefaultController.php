<?php

namespace ROV\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction($page)
    {
    	$numberPosts = 3;

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
    		$numberPosts,
    		0 + ($page * $numberPosts)
    	);

        return $this->render('ROVBlogBundle:Default:blog.html.twig', array(
        	'articles' => $lastArticles,
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error
        ));
    }
}
