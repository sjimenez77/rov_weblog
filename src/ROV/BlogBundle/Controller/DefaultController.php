<?php

namespace ROV\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

use ROV\BlogBundle\Entity\Article;
use ROV\BlogBundle\Entity\Tag;
use ROV\BlogBundle\Entity\Category;
use ROV\BlogBundle\Form\Backend\ArticleType;
use ROV\BlogBundle\Form\Backend\CategoryType;
use ROV\BlogBundle\Form\Backend\TagType;

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
        $user = $this->get('security.context')->getToken()->getUser();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $article = new Article();
        $formNewArticle = $this->createForm(new ArticleType(), $article);
        $category = new Category();
        $formNewCategory = $this->createForm(new CategoryType(), $category);
        $tag = new Tag();
        $formNewTag = $this->createForm(new TagType(), $tag);

        // New in version 2.3: The handleRequest() method was added in Symfony 2.3. 
        // Previously, the $request was passed to the submit method - a strategy which 
        // is deprecated and will be removed in Symfony 3.0.
        $formNewArticle->handleRequest($request);
        if ($formNewArticle->isValid())
        {
            $article->setSlug(str_replace(" ", "_", trim($article->getTitle())));
            $article->setAuthor($user);
            $em->persist($article);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New article saved'
            );

            return $this->render('ROVBlogBundle:Default:newArticle.html.twig', array(
                'new_article_form'  => $formNewArticle->createView(),
                'new_category_form' => $formNewCategory->createView(),
                'new_tag_form'      => $formNewTag->createView(),
                'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
                'error'             => $error
            ));

        }
        $formNewCategory->handleRequest($request);
        if ($formNewCategory->isValid())
        {
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New category added'
            );

            return $this->render('ROVBlogBundle:Default:newArticle.html.twig', array(
                'new_article_form'  => $formNewArticle->createView(),
                'new_category_form' => $formNewCategory->createView(),
                'new_tag_form'      => $formNewTag->createView(),
                'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
                'error'             => $error
            ));

        }
        $formNewTag->handleRequest($request);
        if ($formNewTag->isValid())
        {
            $em->persist($tag);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New tag added'
            );

            return $this->render('ROVBlogBundle:Default:newArticle.html.twig', array(
                'new_article_form'  => $formNewArticle->createView(),
                'new_category_form' => $formNewCategory->createView(),
                'new_tag_form'      => $formNewTag->createView(),
                'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
                'error'             => $error
            ));

        }

        return $this->render('ROVBlogBundle:Default:newArticle.html.twig', array(
            'new_article_form'      => $formNewArticle->createView(),
            'new_category_form'     => $formNewCategory->createView(),
            'new_tag_form'          => $formNewTag->createView(),
            'last_username'         => $session->get(SecurityContext::LAST_USERNAME),
            'error'                 => $error
        ));
    }
}
