<?php

namespace ROV\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

use ROV\BlogBundle\Entity\Article;
use ROV\BlogBundle\Entity\Tag;
use ROV\BlogBundle\Entity\Category;
use ROV\BlogBundle\Form\Backend\ArticleType;

class DefaultController extends Controller
{
    /**
     * Show paginated blog articles
     * @param  Request $request [description]
     * @param  integer $page 
     * @return object           Twig template
     */
    public function blogHomeAction(Request $request, $page)
    {
    	$numberPosts = 3;

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

    	$lastArticles = $em->getRepository('ROVBlogBundle:Article')->findBy(
    		array('published' => true),
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

    /**
     * New article form
     * @param  Request $request [description]
     * @return object           Twig template
     */
    public function newArticleAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $article = new Article();
        $form = $this->createForm(new ArticleType(), $article);
        // New in version 2.3: The handleRequest() method was added in Symfony 2.3. 
        // Previously, the $request was passed to the submit method - a strategy which 
        // is deprecated and will be removed in Symfony 3.0.
        $form->handleRequest($request);
        if ($form->isValid())
        {

        }

        return $this->render('ROVBlogBundle:Default:newArticle.html.twig', array(
            'new_article_form' => $form->createView(),
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error
        ));
    }
}
