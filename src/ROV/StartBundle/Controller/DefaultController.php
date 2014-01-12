<?php

namespace ROV\StartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function homeAction()
    {
    	$em = $this->getDoctrine()->getManager();

        $lastArticles = $em->getRepository('ROVBlogBundle:Article')->findBy(
        	array(),
        	array('updated' => 'DESC'),
        	5,
        	0
        );

        return $this->render('ROVStartBundle:Default:home.html.twig', array(
        	'articles' => $lastArticles
        ));
    }
}
