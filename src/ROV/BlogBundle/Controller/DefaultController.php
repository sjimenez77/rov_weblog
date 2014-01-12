<?php

namespace ROV\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($page)
    {
    	$numberPosts = 3;

    	$em = $this->getDoctrine()->getManager();

    	$lastArticles = $em->getRepository('ROVBlogBundle:Article')->findBy(
    		array(),
    		array('updated' => 'DESC'),
    		$numberPosts,
    		0 + ($page * $numberPosts)
    	);

        return $this->render('ROVBlogBundle:Default:blog.html.twig', array(
        	'articles' => $lastArticles
        ));
    }
}
