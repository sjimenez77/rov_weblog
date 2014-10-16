<?php

namespace ROV\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

use ROV\BlogBundle\Entity\Region;
use ROV\BlogBundle\Entity\Winery;
use ROV\BlogBundle\Entity\Wine;
use ROV\BlogBundle\Form\Backend\RegionType;
use ROV\BlogBundle\Form\Backend\WineryType;
use ROV\BlogBundle\Form\Backend\WineType;

use ROV\BlogBundle\Util\Util;

class WineController extends Controller
{
	/**
	 * Show paginated wine tasting reviews
	 * @param  Request $request [description]
	 * @param  integer $page    [description]
	 * @return object           Twig template
	 */
	public function wineReviewsAction(Request $request, $page)
	{
		$numberReviews = 5;

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        // Search form
        $defaultData = array();
        $formSearch = $this->createFormBuilder($defaultData)
                ->add('search', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type your search'
                    )
                ))
                ->getForm();

        $region = new Region();
        $formNewRegion = $this->createForm(new RegionType(), $region);
        $winery = new Winery();
        $formNewWinery = $this->createForm(new WineryType(), $winery);
        // Form declared to get the wine types labels
        $formWine = $this->createForm(new WineType(), null);
        $wineTypesLabels = $formWine->get('type')->getConfig()->getOption('choices');

        // Get regions
        $regions = $em->getRepository('ROVBlogBundle:Region')->findBy(
                array(),
                array('name' => 'ASC')
            );

        // Get wineries
        $wineries = $em->getRepository('ROVBlogBundle:Winery')->findBy(
                array(),
                array('name' => 'ASC')
            );

        // Get last wine tasting reviews
        $query = $em->createQuery(
        	'SELECT w, wry, r FROM ROVBlogBundle:Wine w
        	 JOIN w.winery wry
        	 JOIN wry.region r
        	 WHERE w.published = :published
        	 ORDER BY w.updated DESC'
        	);
        $query->setParameter('published', true);
        $query->setMaxResults($numberReviews);
        $query->setFirstResult(($page-1) * $numberReviews);
        $lastWineReviews = $query->getResult();

        // Remaining wine tasting reviews
        $queryCount = $em->createQuery('SELECT a FROM ROVBlogBundle:Wine a WHERE a.published = :published');
        $queryCount->setParameter('published', true);
        $queryCount->setFirstResult(($page) * $numberReviews);
        $wineReviewsLeft = $queryCount->getResult();

        // Number of wine tasting reviews by month
        $now = new \DateTime();
        $year = $now->format('Y');
        $queryByMonth = $em->createQuery(
            'SELECT COUNT(a.id) as total, SUBSTRING(a.updated, 6, 2) as month, SUBSTRING(a.updated, 1, 4) as year
             FROM ROVBlogBundle:Wine a
             WHERE SUBSTRING(a.updated, 1, 4) >= :year
             AND a.published = :published
             GROUP BY month');
        $queryByMonth->setParameter('year', ($year - 1));
        $queryByMonth->setParameter('published', true);
        $wineReviewsByMonth = $queryByMonth->getResult();

        return $this->render('ROVBlogBundle:Wines:wines.html.twig', array(
            'page'              => $page,
            'form_search'       => $formSearch->createView(),
            'wineTypesLabels'   => $wineTypesLabels,
        	'wineReviews'       => $lastWineReviews,
            'wineReviewsLeft'   => $wineReviewsLeft,
            'wineReviewsMonth'  => $wineReviewsByMonth,
            'regions'        	=> $regions,
            'wineries'          => $wineries,
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'new_region_form'	=> $formNewRegion->createView(),
            'new_winery_form'   => $formNewWinery->createView(),
            'error'             => $error
        ));
	}

	/**
     * Show paginated wine tasting reviews by region
     * @param  Request $request [description]
     * @param  string  $slug 
     * @param  integer $page 
     * @return object           Twig template
     */
    public function wineReviewsRegionAction(Request $request, $slug, $page)
	{
        $numberReviews = 5;

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        // Search form
        $defaultData = array();
        $formSearch = $this->createFormBuilder($defaultData)
                ->add('search', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type your search'
                    )
                ))
                ->getForm();

        $region = new Region();
        $formNewRegion = $this->createForm(new RegionType(), $region);
        $winery = new Winery();
        $formNewWinery = $this->createForm(new WineryType(), $winery);
        // Form declared to get the wine types labels
        $formWine = $this->createForm(new WineType(), null);
        $wineTypesLabels = $formWine->get('type')->getConfig()->getOption('choices');

        // Get regions
        $regions = $em->getRepository('ROVBlogBundle:Region')->findBy(
                array(),
                array('name' => 'ASC')
            );

        // Get wineries
        $wineries = $em->getRepository('ROVBlogBundle:Winery')->findBy(
                array(),
                array('name' => 'ASC')
            );

        // Get last wine tasting reviews
        $query = $em->createQuery(
            'SELECT w, wry, r FROM ROVBlogBundle:Wine w
             JOIN w.winery wry
             JOIN wry.region r
             WHERE w.published = :published
             AND r.slug = :slug
             ORDER BY w.updated DESC'
            );
        $query->setParameter('published', true);
        $query->setParameter('slug', $slug);
        $query->setMaxResults($numberReviews);
        $query->setFirstResult(($page-1) * $numberReviews);
        $lastWineReviews = $query->getResult();

        // Remaining wine tasting reviews
        $queryCount = $em->createQuery(
            'SELECT w, wry, r FROM ROVBlogBundle:Wine w 
             JOIN w.winery wry
             JOIN wry.region r
             WHERE w.published = :published
             AND r.slug = :slug'
            );
        $queryCount->setParameter('published', true);
        $queryCount->setParameter('slug', $slug);
        $queryCount->setFirstResult(($page) * $numberReviews);
        $wineReviewsLeft = $queryCount->getResult();

        $region = $em->getRepository('ROVBlogBundle:Region')->findOneBy(array('slug' => $slug));

        // Number of wine tasting reviews by month
        $now = new \DateTime();
        $year = $now->format('Y');
        $queryByMonth = $em->createQuery(
            'SELECT COUNT(a.id) as total, SUBSTRING(a.updated, 6, 2) as month, SUBSTRING(a.updated, 1, 4) as year
             FROM ROVBlogBundle:Wine a
             WHERE SUBSTRING(a.updated, 1, 4) >= :year
             AND a.published = :published
             GROUP BY month');
        $queryByMonth->setParameter('year', ($year - 1));
        $queryByMonth->setParameter('published', true);
        $wineReviewsByMonth = $queryByMonth->getResult();

        return $this->render('ROVBlogBundle:Wines:wines.html.twig', array(
            'page'              => $page,
            'region_url'        => $region,
            'form_search'       => $formSearch->createView(),
            'wineTypesLabels'   => $wineTypesLabels,
            'wineReviews'       => $lastWineReviews,
            'wineReviewsLeft'   => $wineReviewsLeft,
            'wineReviewsMonth'  => $wineReviewsByMonth,
            'regions'           => $regions,
            'wineries'          => $wineries,
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'new_region_form'   => $formNewRegion->createView(),
            'new_winery_form'   => $formNewWinery->createView(),
            'error'             => $error
        ));
	}

    /**
     * Show paginated wine tasting reviews by winery
     * @param  Request $request [description]
     * @param  string  $slug 
     * @param  integer $page 
     * @return object           Twig template
     */
	public function wineReviewsWineryAction(Request $request, $slug, $page)
	{
        $numberReviews = 5;

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        // Search form
        $defaultData = array();
        $formSearch = $this->createFormBuilder($defaultData)
                ->add('search', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type your search'
                    )
                ))
                ->getForm();

        $region = new Region();
        $formNewRegion = $this->createForm(new RegionType(), $region);
        $winery = new Winery();
        $formNewWinery = $this->createForm(new WineryType(), $winery);
        // Form declared to get the wine types labels
        $formWine = $this->createForm(new WineType(), null);
        $wineTypesLabels = $formWine->get('type')->getConfig()->getOption('choices');

        // Get regions
        $regions = $em->getRepository('ROVBlogBundle:Region')->findBy(
                array(),
                array('name' => 'ASC')
            );

        // Get wineries
        $wineries = $em->getRepository('ROVBlogBundle:Winery')->findBy(
                array(),
                array('name' => 'ASC')
            );

        // Get last wine tasting reviews
        $query = $em->createQuery(
            'SELECT w, wry, r FROM ROVBlogBundle:Wine w
             JOIN w.winery wry
             JOIN wry.region r
             WHERE w.published = :published
             AND wry.slug = :slug
             ORDER BY w.updated DESC'
            );
        $query->setParameter('published', true);
        $query->setParameter('slug', $slug);
        $query->setMaxResults($numberReviews);
        $query->setFirstResult(($page-1) * $numberReviews);
        $lastWineReviews = $query->getResult();

        // Remaining wine tasting reviews
        $queryCount = $em->createQuery(
            'SELECT w, wry, r FROM ROVBlogBundle:Wine w 
             JOIN w.winery wry
             JOIN wry.region r
             WHERE w.published = :published
             AND wry.slug = :slug'
            );
        $queryCount->setParameter('published', true);
        $queryCount->setParameter('slug', $slug);
        $queryCount->setFirstResult(($page) * $numberReviews);
        $wineReviewsLeft = $queryCount->getResult();

        $winery = $em->getRepository('ROVBlogBundle:Winery')->findOneBy(array('slug' => $slug));

        // Number of wine tasting reviews by month
        $now = new \DateTime();
        $year = $now->format('Y');
        $queryByMonth = $em->createQuery(
            'SELECT COUNT(a.id) as total, SUBSTRING(a.updated, 6, 2) as month, SUBSTRING(a.updated, 1, 4) as year
             FROM ROVBlogBundle:Wine a
             WHERE SUBSTRING(a.updated, 1, 4) >= :year
             AND a.published = :published
             GROUP BY month');
        $queryByMonth->setParameter('year', ($year - 1));
        $queryByMonth->setParameter('published', true);
        $wineReviewsByMonth = $queryByMonth->getResult();

        return $this->render('ROVBlogBundle:Wines:wines.html.twig', array(
            'page'              => $page,
            'winery_url'        => $winery,
            'form_search'       => $formSearch->createView(),
            'wineTypesLabels'   => $wineTypesLabels,
            'wineReviews'       => $lastWineReviews,
            'wineReviewsLeft'   => $wineReviewsLeft,
            'wineReviewsMonth'  => $wineReviewsByMonth,
            'regions'           => $regions,
            'wineries'          => $wineries,
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'new_region_form'   => $formNewRegion->createView(),
            'new_winery_form'   => $formNewWinery->createView(),
            'error'             => $error
        ));
	}

    /**
     * Show paginated wine tasting reviews by date
     * @param  Request $request [description]
     * @param  integer $year 
     * @param  integer $month 
     * @param  integer $page 
     * @return object           Twig template
     */
	public function wineReviewsDateAction(Request $request, $year, $month, $page)
	{
		$numberReviews = 5;

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        // Search form
        $defaultData = array();
        $formSearch = $this->createFormBuilder($defaultData)
                ->add('search', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type your search'
                    )
                ))
                ->getForm();

        $region = new Region();
        $formNewRegion = $this->createForm(new RegionType(), $region);
        $winery = new Winery();
        $formNewWinery = $this->createForm(new WineryType(), $winery);
        // Form declared to get the wine types labels
        $formWine = $this->createForm(new WineType(), null);
        $wineTypesLabels = $formWine->get('type')->getConfig()->getOption('choices');

        // Get regions
        $regions = $em->getRepository('ROVBlogBundle:Region')->findBy(
                array(),
                array('name' => 'ASC')
            );

        // Get wineries
        $wineries = $em->getRepository('ROVBlogBundle:Winery')->findBy(
                array(),
                array('name' => 'ASC')
            );

        // Check if month parameter is set
        if ($month > 0)
        {
            $startDate = new \DateTime($year.'-'.$month.'-'.'01');
            $endDate = new \DateTime($year.'-'.($month+1).'-'.'01');            
        }
        else
        {
            $startDate = new \DateTime($year.'-01-01');
            $endDate = new \DateTime(($year+1).'-01-01');            
        }

        // Get last wine tasting reviews
        $query = $em->createQuery(
            'SELECT w, wry, r FROM ROVBlogBundle:Wine w
             JOIN w.winery wry
             JOIN wry.region r
             WHERE w.published = :published
             AND w.updated >= :start
             AND w.updated < :end
             ORDER BY w.updated DESC'
            );
        $query->setParameter('published', true);
        $query->setParameter('start', $startDate);
        $query->setParameter('end', $endDate);
        $query->setMaxResults($numberReviews);
        $query->setFirstResult(($page-1) * $numberReviews);
        $lastWineReviews = $query->getResult();

        // Remaining wine tasting reviews
        $queryCount = $em->createQuery(
            'SELECT a FROM ROVBlogBundle:Wine a
             WHERE a.published = :published
             AND a.updated >= :start
             AND a.updated < :end');
        $queryCount->setParameter('published', true);
        $queryCount->setParameter('start', $startDate);
        $queryCount->setParameter('end', $endDate);
        $queryCount->setFirstResult(($page) * $numberReviews);
        $wineReviewsLeft = $queryCount->getResult();

        // Number of wine tasting reviews by month
        $now = new \DateTime();
        $year = $now->format('Y');
        $queryByMonth = $em->createQuery(
            'SELECT COUNT(a.id) as total, SUBSTRING(a.updated, 6, 2) as month, SUBSTRING(a.updated, 1, 4) as year
             FROM ROVBlogBundle:Wine a
             WHERE SUBSTRING(a.updated, 1, 4) >= :year
             AND a.published = :published
             GROUP BY month');
        $queryByMonth->setParameter('year', ($year - 1));
        $queryByMonth->setParameter('published', true);
        $wineReviewsByMonth = $queryByMonth->getResult();

        return $this->render('ROVBlogBundle:Wines:wines.html.twig', array(
            'page'              => $page,
            'year_url'          => $year,
            'month_url'         => $month,
            'form_search'       => $formSearch->createView(),
            'wineTypesLabels'   => $wineTypesLabels,
            'wineReviews'       => $lastWineReviews,
            'wineReviewsLeft'   => $wineReviewsLeft,
            'wineReviewsMonth'  => $wineReviewsByMonth,
            'regions'           => $regions,
            'wineries'          => $wineries,
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'new_region_form'   => $formNewRegion->createView(),
            'new_winery_form'   => $formNewWinery->createView(),
            'error'             => $error
        ));
	}

    public function previewWineAction(Request $request, $wine_id)
    {
        # code...
    }

    /**
     * New wine tasting review form
     * @param  Request $request [description]
     * @return object           Twig template
     */
	public function newWineAction(Request $request)
	{
		$session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $wine = new Wine();
        $formNewWine = $this->createForm(new WineType(), $wine);
        $region = new Region();
        $formNewRegion = $this->createForm(new RegionType(), $region);
        $winery = new Winery();
        $formNewWinery = $this->createForm(new WineryType(), $winery);

        // New in version 2.3: The handleRequest() method was added in Symfony 2.3. 
        // Previously, the $request was passed to the submit method - a strategy which 
        // is deprecated and will be removed in Symfony 3.0.
        $formNewWine->handleRequest($request);
        if ($formNewWine->isValid())
        {
            // Process title and create a valid slug
            $slug = Util::getSlug($wine->getBrand());

            $wine->setSlug($slug);
            $wine->setAuthor($user);
            $em->persist($wine);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New wine tasting review saved successfully'
            );

            return $this->redirect($this->generateUrl('rov_blog_manage_wines'));
        }

        $formNewRegion->handleRequest($request);
        if ($formNewRegion->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($region->getName());
            $region->setSlug($slug);
            $em->persist($region);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New region added'
            );
        }

        $formNewWinery->handleRequest($request);
        if ($formNewWinery->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($winery->getName());
            $winery->setSlug($slug);
            $em->persist($winery);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New winery added'
            );
        }

        $regions = $em->getRepository('ROVBlogBundle:Region')->findBy(
                array(),
                array('name' => 'ASC')
            );
        $wineries = $em->getRepository('ROVBlogBundle:Winery')->findBy(
                array(),
                array('name' => 'ASC')
            );

        return $this->render('ROVBlogBundle:Wines:newWine.html.twig', array(
            'new_wine_form'     => $formNewWine->createView(),
            'new_region_form'   => $formNewRegion->createView(),
            'new_winery_form'   => $formNewWinery->createView(),
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'error'             => $error
        ));
	}

	/**
     * Edit wine tasting review form
     * @param  Request $request [description]
     * @param  integer $wine_id [description]
     * @return object           Twig template
     */
    public function editWineAction(Request $request, $wine_id)
	{
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $wine = $em->getRepository('ROVBlogBundle:Wine')->find($wine_id);
        $formEditWine = $this->createForm(new WineType(), $wine);
        $region = new Region();
        $formNewRegion = $this->createForm(new RegionType(), $region);
        $winery = new Winery();
        $formNewWinery = $this->createForm(new WineryType(), $winery);

        // New in version 2.3: The handleRequest() method was added in Symfony 2.3. 
        // Previously, the $request was passed to the submit method - a strategy which 
        // is deprecated and will be removed in Symfony 3.0.
        $formEditWine->handleRequest($request);
        if ($formEditWine->isValid())
        {
            // Process title and create a valid slug
            $slug = Util::getSlug($wine->getBrand());

            $wine->setSlug($slug);
            $wine->setUpdated(new \DateTime());
            $em->persist($wine);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'Wine tasting review updated successfully'
            );

            return $this->redirect($this->generateUrl('rov_blog_manage_wines'));
        }

        $formNewRegion->handleRequest($request);
        if ($formNewRegion->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($region->getName());
            $region->setSlug($slug);
            $em->persist($region);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New region added'
            );
        }

        $formNewWinery->handleRequest($request);
        if ($formNewWinery->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($winery->getName());
            $winery->setSlug($slug);
            $em->persist($winery);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New winery added'
            );
        }

        $regions = $em->getRepository('ROVBlogBundle:Region')->findBy(
                array(),
                array('name' => 'ASC')
            );
        $wineries = $em->getRepository('ROVBlogBundle:Winery')->findBy(
                array(),
                array('name' => 'ASC')
            );

        return $this->render('ROVBlogBundle:Wines:editWine.html.twig', array(
            'edit_wine_form'    => $formEditWine->createView(),
            'wine_id'           => $wine_id,
            'new_region_form'   => $formNewRegion->createView(),
            'new_winery_form'   => $formNewWinery->createView(),
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'error'             => $error
        ));
	}

	/**
     * Manage the wine tasting reviews
     * @param  Request $request [description]
     * @return object           Twig template
     */
    public function manageWinesAction(Request $request)
	{
		$session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $region = new Region();
        $formNewRegion = $this->createForm(new RegionType(), $region);
        $winery = new Winery();
        $formNewWinery = $this->createForm(new WineryType(), $winery);

        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
        {
            // Get all the wine tasting reviews
            $query = $em->createQuery(
                'SELECT w, wry, r FROM ROVBlogBundle:Wine w
                 JOIN w.winery wry
                 JOIN wry.region r
                 ORDER BY w.updated DESC'
                );
            $wineReviews = $query->getResult();
        }
        else
        {
            // Get the wine tasting reviews from the author $user
            $user = $this->get('security.context')->getToken()->getUser();
            $query = $em->createQuery(
                'SELECT w, wry, r FROM ROVBlogBundle:Wine w
                 JOIN w.winery wry
                 JOIN wry.region r
                 WHERE w.author = :author
                 ORDER BY w.updated DESC'
                );
            $query->setParameter('author', $user);
            $wineReviews = $query->getResult();
        }

        $formNewRegion->handleRequest($request);
        if ($formNewRegion->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($region->getName());
            $region->setSlug($slug);
            $em->persist($region);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New region added'
            );
        }

        $formNewWinery->handleRequest($request);
        if ($formNewWinery->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($winery->getName());
            $winery->setSlug($slug);
            $em->persist($winery);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'New winery added'
            );
        }

        $regions = $em->getRepository('ROVBlogBundle:Region')->findBy(
                array(),
                array('name' => 'ASC')
            );
        $wineries = $em->getRepository('ROVBlogBundle:Winery')->findBy(
                array(),
                array('name' => 'ASC')
            );

        return $this->render('ROVBlogBundle:Wines:manageWineReviews.html.twig', array(
            'wineReviews'           => $wineReviews,
            'regions'               => $regions,
            'wineries'              => $wineries,
            'new_region_form'       => $formNewRegion->createView(),
            'new_winery_form'       => $formNewWinery->createView(),
            'last_username'         => $session->get(SecurityContext::LAST_USERNAME),
            'error'                 => $error
        ));
	}

    /**
     * Edit regions
     * @param  Request $request [description]
     * @return object           Twig template
     */
    public function manageRegionAction(Request $request, $slug)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $region = $em->getRepository('ROVBlogBundle:Region')->findOneBy(array('slug' => $slug));
        $formEditRegion = $this->createForm(new RegionType(), $region);
        $formEditRegion->handleRequest($request);
        if ($formEditRegion->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($region->getName());
            $region->setSlug($slug);
            $em->persist($region);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'Region updated'
            );

            return $this->redirect($this->generateUrl('rov_blog_manage_wines'));
        }

        return $this->render('ROVBlogBundle:Wines:manageRegion.html.twig', array(
            'region'            => $region,
            'edit_region_form'  => $formEditRegion->createView(),
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'error'             => $error
        ));
    }

    /**
     * Edit winery
     * @param  Request $request [description]
     * @return object           Twig template
     */
    public function manageWineryAction(Request $request, $slug)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        $winery = $em->getRepository('ROVBlogBundle:Winery')->findOneBy(array('slug' => $slug));
        $formEditWinery = $this->createForm(new WineryType(), $winery);
        $formEditWinery->handleRequest($request);
        if ($formEditWinery->isValid())
        {
            // Create a valid slug
            $slug = Util::getSlug($winery->getName());
            $winery->setSlug($slug);
            $em->persist($winery);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'Winery updated'
            );

            return $this->redirect($this->generateUrl('rov_blog_manage_wines'));
        }

        return $this->render('ROVBlogBundle:Wines:manageWinery.html.twig', array(
            'winery'            => $winery,
            'edit_winery_form'  => $formEditWinery->createView(),
            'last_username'     => $session->get(SecurityContext::LAST_USERNAME),
            'error'             => $error
        ));
    }
}
