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
            // Process title and create a valid slug
            $some_special_chars = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ");
            $replacement_chars  = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N");
            $slug = str_replace(" ", "_", trim($article->getTitle()));
            $slug = str_replace($some_special_chars, $replacement_chars, $slug);

            $article->setSlug($slug);
            $article->setAuthor($user);
            $em->persist($article);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New article saved'
            );

            return $this->redirect($this->generateUrl('rov_blog_manage_articles'));
        }

        $formNewCategory->handleRequest($request);
        if ($formNewCategory->isValid())
        {
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New category added'
            );
        }

        $formNewTag->handleRequest($request);
        if ($formNewTag->isValid())
        {
            $em->persist($tag);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New tag added'
            );
        }

        return $this->render('ROVBlogBundle:Default:newArticle.html.twig', array(
            'new_article_form'      => $formNewArticle->createView(),
            'new_category_form'     => $formNewCategory->createView(),
            'new_tag_form'          => $formNewTag->createView(),
            'last_username'         => $session->get(SecurityContext::LAST_USERNAME),
            'error'                 => $error
        ));
    }

    /**
     * Edit article form
     * @param  Request $request [description]
     * @param  integer $article_id [description]
     * @return object           Twig template
     */
    public function editArticleAction(Request $request, $article_id)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $article = $em->getRepository('ROVBlogBundle:Article')->find($article_id);
        $formEditArticle = $this->createForm(new ArticleType(), $article);
        $category = new Category();
        $formNewCategory = $this->createForm(new CategoryType(), $category);
        $tag = new Tag();
        $formNewTag = $this->createForm(new TagType(), $tag);

        // New in version 2.3: The handleRequest() method was added in Symfony 2.3. 
        // Previously, the $request was passed to the submit method - a strategy which 
        // is deprecated and will be removed in Symfony 3.0.
        $formEditArticle->handleRequest($request);
        if ($formEditArticle->isValid())
        {
            // Process title and create a valid slug
            $some_special_chars = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ");
            $replacement_chars  = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N");
            $slug = str_replace(" ", "_", trim($article->getTitle()));
            $slug = str_replace($some_special_chars, $replacement_chars, $slug);

            $article->setSlug($slug);
            $article->setUpdated(new \DateTime());
            $em->persist($article);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'Article saved'
            );
        }

        $formNewCategory->handleRequest($request);
        if ($formNewCategory->isValid())
        {
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New category added'
            );
        }

        $formNewTag->handleRequest($request);
        if ($formNewTag->isValid())
        {
            $em->persist($tag);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New tag added'
            );
        }

        return $this->render('ROVBlogBundle:Default:editArticle.html.twig', array(
            'edit_article_form'     => $formEditArticle->createView(),
            'article_id'            => $article_id,
            'new_category_form'     => $formNewCategory->createView(),
            'new_tag_form'          => $formNewTag->createView(),
            'last_username'         => $session->get(SecurityContext::LAST_USERNAME),
            'error'                 => $error
        ));
    }

    /**
     * Delete article
     * @param  Request $request [description]
     * @param  integer $article_id [description]
     * @return object           Twig template
     */
    public function deleteArticleAction(Request $request, $article_id)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $article = $em->getRepository('ROVBlogBundle:Article')->find($article_id);
            $em->remove($article);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'Article deleted successfully'
            );
        }
        elseif ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            // Check whether the article belongs to this user
            $article = $em->getRepository('ROVBlogBundle:Article')->findBy(array(
                'id' => $article_id,
                'author' => $user
                ));

            if ($article) {
                $em->remove($article);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success',
                    'Article deleted successfully'
                );
            } else {
                $this->get('session')->getFlashBag()->add('error',
                    'You cannot delete this article'
                );
            }

        } else {
            // User has not an admin role
            $this->get('session')->getFlashBag()->add('error',
                'You cannot delete articles'
            );
            return $this->redirect($this->generateUrl('rov_blog_homepage'));            
        }

        return $this->redirect($this->generateUrl('rov_blog_manage_articles'));
    }

    /**
     * Manage the articles
     * @param  Request $request [description]
     * @return object           Twig template
     */
    public function manageArticlesAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $category = new Category();
        $formNewCategory = $this->createForm(new CategoryType(), $category);
        $tag = new Tag();
        $formNewTag = $this->createForm(new TagType(), $tag);

        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
        {
            // Get all the articles
            $articles = $em->getRepository('ROVBlogBundle:Article')->findBy(
                array(),
                array('updated' => 'DESC')
            );
        }
        else
        {
            // Get the articles from the author $user
            $user = $this->get('security.context')->getToken()->getUser();
            $articles = $em->getRepository('ROVBlogBundle:Article')->findBy(
                array('author' => $user),
                array('updated' => 'DESC')
            );
        }

        
        $formNewCategory->handleRequest($request);
        if ($formNewCategory->isValid())
        {
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New category added'
            );
        }

        $formNewTag->handleRequest($request);
        if ($formNewTag->isValid())
        {
            $em->persist($tag);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New tag added'
            );
        }

        $categories = $em->getRepository('ROVBlogBundle:Category')->findBy(
                array(),
                array('name' => 'ASC')
            );
        $tags = $em->getRepository('ROVBlogBundle:Tag')->findBy(
                array(),
                array('name' => 'ASC')
            );

        return $this->render('ROVBlogBundle:Default:manageArticles.html.twig', array(
            'articles'              => $articles,
            'categories'            => $categories,
            'tags'                  => $tags,
            'new_category_form'     => $formNewCategory->createView(),
            'new_tag_form'          => $formNewTag->createView(),
            'last_username'         => $session->get(SecurityContext::LAST_USERNAME),
            'error'                 => $error
        ));
    }
}
