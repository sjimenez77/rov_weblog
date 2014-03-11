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

use ROV\BlogBundle\Util\Util;

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

        $defaultData = array();
        $formSearch = $this->createFormBuilder($defaultData)
                ->add('search', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type your search'
                    )
                ))
                ->getForm();

        $category = new Category();
        $formNewCategory = $this->createForm(new CategoryType(), $category);
        $tag = new Tag();
        $formNewTag = $this->createForm(new TagType(), $tag);

        $categories = $em->getRepository('ROVBlogBundle:Category')->findBy(
                array(),
                array('name' => 'ASC')
            );
        $tags = $em->getRepository('ROVBlogBundle:Tag')->findBy(
                array(),
                array('name' => 'ASC')
            );

        $query = $em->createQuery('
            SELECT a, u FROM ROVBlogBundle:Article a JOIN a.author u
            WHERE a.published = :published
            ORDER BY a.updated DESC');
        $query->setParameter('published', true);
        $query->setMaxResults($numberPosts);
        $query->setFirstResult(($page-1) * $numberPosts);
        $lastArticles = $query->getResult();

        $queryCount = $em->createQuery('SELECT a FROM ROVBlogBundle:Article a WHERE a.published = :published');
        $queryCount->setParameter('published', true);
        $queryCount->setFirstResult(($page) * $numberPosts);
        $articlesLeft = $queryCount->getResult();

        return $this->render('ROVBlogBundle:Default:blog.html.twig', array(
            'page'              => $page,
            'form_search'       => $formSearch->createView(),
        	'articles'          => $lastArticles,
            'articlesLeft'      => $articlesLeft,
            'categories'        => $categories,
            'tags'              => $tags,
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'new_category_form' => $formNewCategory->createView(),
            'new_tag_form'      => $formNewTag->createView(),
            'error'             => $error
        ));
    }

    /**
     * Show paginated blog articles
     * @param  Request $request [description]
     * @return object           Twig template
     */
    public function articleCategoryAction(Request $request, $slug, $page)
    {
        $numberPosts = 3;

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
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

        $category = new Category();
        $formNewCategory = $this->createForm(new CategoryType(), $category);
        $tag = new Tag();
        $formNewTag = $this->createForm(new TagType(), $tag);

        $categories = $em->getRepository('ROVBlogBundle:Category')->findBy(
                array(),
                array('name' => 'ASC')
            );
        $tags = $em->getRepository('ROVBlogBundle:Tag')->findBy(
                array(),
                array('name' => 'ASC')
            );

        $query = $em->createQuery('
            SELECT a, u, c FROM ROVBlogBundle:Article a 
            JOIN a.author u
            JOIN a.category c
            WHERE a.published = :published
            AND c.slug = :slug
            ORDER BY a.updated DESC');
        $query->setParameter('published', true);
        $query->setParameter('slug', $slug);
        $query->setMaxResults($numberPosts);
        $query->setFirstResult(($page-1) * $numberPosts);
        $lastArticles = $query->getResult();

        $queryCount = $em->createQuery('
            SELECT a FROM ROVBlogBundle:Article a
            JOIN a.category c
            WHERE a.published = :published
            AND c.slug = :slug');
        $queryCount->setParameter('published', true);
        $queryCount->setParameter('slug', $slug);
        $queryCount->setFirstResult(($page) * $numberPosts);
        $articlesLeft = $queryCount->getResult();

        $category = $em->getRepository('ROVBlogBundle:Category')->findOneBy(array('slug' => $slug));

        return $this->render('ROVBlogBundle:Default:blog.html.twig', array(
            'page'              => $page,
            'category_url'      => $category,
            'form_search'       => $formSearch->createView(),
            'articles'          => $lastArticles,
            'articlesLeft'      => $articlesLeft,
            'categories'        => $categories,
            'tags'              => $tags,
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'new_category_form' => $formNewCategory->createView(),
            'new_tag_form'      => $formNewTag->createView(),
            'error'             => $error
        ));
    }

    /**
     * Show paginated blog articles
     * @param  Request $request [description]
     * @return object           Twig template
     */
    public function articleTagAction(Request $request, $slug, $page)
    {
        $numberPosts = 3;

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
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

        $category = new Category();
        $formNewCategory = $this->createForm(new CategoryType(), $category);
        $tag = new Tag();
        $formNewTag = $this->createForm(new TagType(), $tag);

        $categories = $em->getRepository('ROVBlogBundle:Category')->findBy(
                array(),
                array('name' => 'ASC')
            );
        $tags = $em->getRepository('ROVBlogBundle:Tag')->findBy(
                array(),
                array('name' => 'ASC')
            );

        $query = $em->createQuery('
            SELECT a, u, t FROM ROVBlogBundle:Article a 
            JOIN a.author u
            JOIN a.tags t
            WHERE a.published = :published
            AND t.slug = :slug
            ORDER BY a.updated DESC');
        $query->setParameter('published', true);
        $query->setParameter('slug', $slug);
        $query->setMaxResults($numberPosts);
        $query->setFirstResult(($page-1) * $numberPosts);
        $lastArticles = $query->getResult();

        $queryCount = $em->createQuery('
            SELECT a FROM ROVBlogBundle:Article a 
            JOIN a.tags t
            WHERE a.published = :published
            AND t.slug = :slug');
        $queryCount->setParameter('published', true);
        $queryCount->setParameter('slug', $slug);
        $queryCount->setFirstResult(($page) * $numberPosts);
        $articlesLeft = $queryCount->getResult();

        $tag = $em->getRepository('ROVBlogBundle:Tag')->findOneBy(array('slug' => $slug));

        return $this->render('ROVBlogBundle:Default:blog.html.twig', array(
            'page'              => $page,
            'tag_url'               => $tag,
            'form_search'       => $formSearch->createView(),
            'articles'          => $lastArticles,
            'articlesLeft'      => $articlesLeft,
            'categories'        => $categories,
            'tags'              => $tags,
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'new_category_form' => $formNewCategory->createView(),
            'new_tag_form'      => $formNewTag->createView(),
            'error'             => $error
        ));
    }

    /**
     * Search results
     * @param  Request $request [description]
     * @return object           Twig template
     */
    public function articleSearchAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
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

        // Handle formSearch
        $formSearch->handleRequest($request);
        if ($formSearch->isValid())
        {
            $data = $formSearch->getData();
            $querySearch = $em->createQuery('
                SELECT a, u, c, t FROM ROVBlogBundle:Article a 
                JOIN a.author u
                JOIN a.category c
                JOIN a.tags t
                WHERE a.published = :published
                AND (a.title LIKE :search
                    OR a.subtitle LIKE :search
                    OR a.content LIKE :search
                    OR c.name LIKE :search
                    OR t.name LIKE :search)
                ORDER BY a.updated DESC');
            $querySearch->setParameter('published', true);
            $querySearch->setParameter('search', '%'.$data['search'].'%');
            $articlesSearch = $querySearch->getResult();

            $defaultData = array();
            $formSearch = $this->createFormBuilder($defaultData)
                    ->add('search', 'text', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Type your search'
                        )
                    ))
                    ->getForm();

            $category = new Category();
            $formNewCategory = $this->createForm(new CategoryType(), $category);
            $tag = new Tag();
            $formNewTag = $this->createForm(new TagType(), $tag);

            $categories = $em->getRepository('ROVBlogBundle:Category')->findBy(
                    array(),
                    array('name' => 'ASC')
                );
            $tags = $em->getRepository('ROVBlogBundle:Tag')->findBy(
                    array(),
                    array('name' => 'ASC')
                );

            return $this->render('ROVBlogBundle:Default:search.html.twig', array(
                'search_term'       => $data['search'],
                'form_search'       => $formSearch->createView(),
                'articles'          => $articlesSearch,
                'categories'        => $categories,
                'tags'              => $tags,
                'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
                'new_category_form' => $formNewCategory->createView(),
                'new_tag_form'      => $formNewTag->createView(),
                'error'             => $error
            ));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('error',
                'Search error'
            );
            return $this->redirect($this->generateUrl('rov_blog_homepage'));
        }
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
            $slug = Util::getSlug($article->getTitle());

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
            // Create a valid slug
            $slug = Util::getSlug($category->getName());
            $category->setSlug($slug);
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New category added'
            );
        }

        $formNewTag->handleRequest($request);
        if ($formNewTag->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($tag->getName());
            $tag->setSlug($slug);
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
            $slug = Util::getSlug($article->getTitle());

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
            // Create a valid slug
            $slug = Util::getSlug($category->getName());
            $category->setSlug($slug);
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New category added'
            );
        }

        $formNewTag->handleRequest($request);
        if ($formNewTag->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($tag->getName());
            $tag->setSlug($slug);
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
            // Create a valid slug
            $slug = Util::getSlug($category->getName());
            $category->setSlug($slug);
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New category added'
            );
        }

        $formNewTag->handleRequest($request);
        if ($formNewTag->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($tag->getName());
            $tag->setSlug($slug);
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

    /**
     * Edit categories
     * @param  Request $request [description]
     * @return object           Twig template
     */
    public function manageCategoryAction(Request $request, $slug)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $category = $em->getRepository('ROVBlogBundle:Category')->findOneBy(array('slug' => $slug));
        $formEditCategory = $this->createForm(new CategoryType(), $category);
        if ($formEditCategory->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($category->getName());
            $category->setSlug($slug);
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'Category updated'
            );

        }
        return $this->render('ROVBlogBundle:Default:manageCategory.html.twig', array(
            'edit_category_form'    => $formEditCategory->createView(),
            'last_username'         => $session->get(SecurityContext::LAST_USERNAME),
            'error'                 => $error
        ));
    }

    /**
     * Edit tags
     * @param  Request $request [description]
     * @return object           Twig template
     */
    public function manageTagAction(Request $request, $slug)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $tag = $em->getRepository('ROVBlogBundle:Tag')->findOneBy(array('slug' => $slug));
        $formEditTag = $this->createForm(new TagType(), $tag);
        if ($formEditTag->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($tag->getName());
            $tag->setSlug($slug);
            $em->persist($tag);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'Tag updated'
            );
        }
        return $this->render('ROVBlogBundle:Default:manageTag.html.twig', array(
            'edit_tag_form'          => $formEditTag->createView(),
            'last_username'         => $session->get(SecurityContext::LAST_USERNAME),
            'error'                 => $error
        ));
    }
}
