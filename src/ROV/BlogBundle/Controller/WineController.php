<?php

namespace ROV\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;

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
		// Get authentication utils
  	$helper = $this->get('security.authentication_utils');

    // Get the login error if there is any
    $error = $helper->getLastAuthenticationError();

    // Search form
    $defaultData = array();
    $formSearch = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class, array(
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
    $wineTypesLabels = array_flip($formWine->get('type')->getConfig()->getOption('choices'));

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
    	 ORDER BY w.updated DESC');
    $query->setParameter('published', true);
    $query->setMaxResults($numberReviews);
    $query->setFirstResult(($page-1) * $numberReviews);
    $lastWineReviews = $query->getResult();

    // Remaining wine tasting reviews
    $queryCount = $em->createQuery('SELECT w FROM ROVBlogBundle:Wine w WHERE w.published = :published');
    $queryCount->setParameter('published', true);
    $queryCount->setFirstResult(($page) * $numberReviews);
    $wineReviewsLeft = $queryCount->getResult();

    // Number of wine tasting reviews by month
    $now = new \DateTime();
    $year = $now->format('Y');
    $queryByMonth = $em->createQuery(
        'SELECT COUNT(w.id) as total, SUBSTRING(w.updated, 6, 2) as month, SUBSTRING(w.updated, 1, 4) as year
         FROM ROVBlogBundle:Wine w
         WHERE SUBSTRING(w.updated, 1, 4) >= :year
         AND w.published = :published
         GROUP BY month
         ORDER BY w.updated DESC');
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
      'regions'        		=> $regions,
      'wineries'          => $wineries,
      'last_username'     => $helper->getLastUsername(),
      'new_region_form'		=> $formNewRegion->createView(),
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
		// Get authentication utils
  	$helper = $this->get('security.authentication_utils');

    // Get the login error if there is any
    $error = $helper->getLastAuthenticationError();

    // Search form
    $defaultData = array();
    $formSearch = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class, array(
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
		$wineTypesLabels = array_flip($formWine->get('type')->getConfig()->getOption('choices'));

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
        'SELECT COUNT(w.id) as total, SUBSTRING(w.updated, 6, 2) as month, SUBSTRING(w.updated, 1, 4) as year
         FROM ROVBlogBundle:Wine w
         WHERE SUBSTRING(w.updated, 1, 4) >= :year
         AND w.published = :published
         GROUP BY month
         ORDER BY w.updated DESC');
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
        'last_username'     => $helper->getLastUsername(),
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
		// Get authentication utils
  	$helper = $this->get('security.authentication_utils');

    // Get the login error if there is any
    $error = $helper->getLastAuthenticationError();

    // Search form
    $defaultData = array();
    $formSearch = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class, array(
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
		$wineTypesLabels = array_flip($formWine->get('type')->getConfig()->getOption('choices'));

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
        'SELECT COUNT(w.id) as total, SUBSTRING(w.updated, 6, 2) as month, SUBSTRING(w.updated, 1, 4) as year
         FROM ROVBlogBundle:Wine w
         WHERE SUBSTRING(w.updated, 1, 4) >= :year
         AND w.published = :published
         GROUP BY month
         ORDER BY w.updated DESC');
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
        'last_username'     => $helper->getLastUsername(),
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
		// Get authentication utils
  	$helper = $this->get('security.authentication_utils');

    // Get the login error if there is any
    $error = $helper->getLastAuthenticationError();

    // Search form
    $defaultData = array();
    $formSearch = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class, array(
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
		$wineTypesLabels = array_flip($formWine->get('type')->getConfig()->getOption('choices'));

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
        'SELECT w FROM ROVBlogBundle:Wine w
         WHERE w.published = :published
         AND w.updated >= :start
         AND w.updated < :end');
    $queryCount->setParameter('published', true);
    $queryCount->setParameter('start', $startDate);
    $queryCount->setParameter('end', $endDate);
    $queryCount->setFirstResult(($page) * $numberReviews);
    $wineReviewsLeft = $queryCount->getResult();

    // Number of wine tasting reviews by month
    $now = new \DateTime();
    $year = $now->format('Y');
    $queryByMonth = $em->createQuery(
        'SELECT COUNT(w.id) as total, SUBSTRING(w.updated, 6, 2) as month, SUBSTRING(w.updated, 1, 4) as year
         FROM ROVBlogBundle:Wine w
         WHERE SUBSTRING(w.updated, 1, 4) >= :year
         AND w.published = :published
         GROUP BY month
         ORDER BY w.updated DESC');
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
        'last_username'     => $helper->getLastUsername(),
        'new_region_form'   => $formNewRegion->createView(),
        'new_winery_form'   => $formNewWinery->createView(),
        'error'             => $error
    ));
	}


  /**
   * Search results
   * @param  Request $request [description]
   * @return object           Twig template
   */
  public function wineReviewSearchAction(Request $request)
  {
      $numberReviews = 5;

      $session = $request->getSession();
      $em = $this->getDoctrine()->getManager();
			// Get authentication utils
	  	$helper = $this->get('security.authentication_utils');

	    // Get the login error if there is any
	    $error = $helper->getLastAuthenticationError();

      // Search form
      $defaultData = array();
      $formSearch = $this->createFormBuilder($defaultData)
              ->add('search', TextType::class, array(
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
          // Get last wine tasting reviews
          $querySearch = $em->createQuery(
              'SELECT w, wry, r FROM ROVBlogBundle:Wine w
               JOIN w.winery wry
               JOIN wry.region r
               WHERE w.published = :published
               AND (w.brand LIKE :search
                   OR w.varieties LIKE :search
                   OR w.year LIKE :search
                   OR w.wineMaking LIKE :search
                   OR w.description LIKE :search
                   OR wry.name LIKE :search
                   OR wry.location LIKE :search
                   OR wry.address LIKE :search
                   OR r.name LIKE :search)
               ORDER BY w.updated DESC');
          $querySearch->setParameter('published', true);
          $querySearch->setParameter('search', '%'.$data['search'].'%');
          $wineReviews = $querySearch->getResult();

          $region = new Region();
          $formNewRegion = $this->createForm(new RegionType(), $region);
          $winery = new Winery();
          $formNewWinery = $this->createForm(new WineryType(), $winery);
          // Form declared to get the wine types labels
          $formWine = $this->createForm(new WineType(), null);
					$wineTypesLabels = array_flip($formWine->get('type')->getConfig()->getOption('choices'));

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

          // Number of wine tasting reviews by month
          $now = new \DateTime();
          $year = $now->format('Y');
          $queryByMonth = $em->createQuery(
              'SELECT COUNT(w.id) as total, SUBSTRING(w.updated, 6, 2) as month, SUBSTRING(w.updated, 1, 4) as year
               FROM ROVBlogBundle:Wine w
               WHERE SUBSTRING(w.updated, 1, 4) >= :year
               AND w.published = :published
               GROUP BY month
               ORDER BY w.updated DESC');
          $queryByMonth->setParameter('year', ($year - 1));
          $queryByMonth->setParameter('published', true);
          $wineReviewsByMonth = $queryByMonth->getResult();

          return $this->render('ROVBlogBundle:Wines:wineSearch.html.twig', array(
              'search_term'       => $data['search'],
              'form_search'       => $formSearch->createView(),
              'wineTypesLabels'   => $wineTypesLabels,
              'wineReviews'       => $wineReviews,
              'wineReviewsMonth'  => $wineReviewsByMonth,
              'regions'           => $regions,
              'wineries'          => $wineries,
              'last_username'     => $helper->getLastUsername(),
              'new_region_form'   => $formNewRegion->createView(),
              'new_winery_form'   => $formNewWinery->createView(),
              'error'             => $error
          ));
      }
      else
      {
          $this->get('session')->getFlashBag()->add('error',
              'You have changed the language settings, so you must type a new search'
          );

          return $this->render('ROVBlogBundle:Wines:emptyWineSearch.html.twig', array(
              'form_search'       => $formSearch->createView(),
              'last_username'     => $helper->getLastUsername(),
              'error'             => $error
          ));
      }
  }

  /**
   * Show a wine tasting review
   * @param  Request $request
   * @param  string  $slug
   * @return object           Twig template
   */
  public function wineAction(Request $request, $slug)
  {
      $session = $request->getSession();
      $user = $this->get('security.token_storage')->getToken()->getUser();
      $em = $this->getDoctrine()->getManager();
			// Get authentication utils
	  	$helper = $this->get('security.authentication_utils');

	    // Get the login error if there is any
	    $error = $helper->getLastAuthenticationError();

      // Wine review
      $query = $em->createQuery(
          'SELECT w, wry, r FROM ROVBlogBundle:Wine w
           JOIN w.winery wry
           JOIN wry.region r
           WHERE w.published = :published
           AND w.slug = :slug'
          );
      $query->setParameter('published', true);
      $query->setParameter('slug', $slug);
      $wine = $query->getSingleResult();

      // Check whether the wine tasting review exists and it is already published
      if (!$wine)
      {
          $this->get('session')->getFlashBag()->add('error',
              'The wine tasting review does not exist or is not published yet'
          );

          return $this->redirect($this->generateUrl('rov_blog_wine_homepage'));
      }

      if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') ||
         ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $wine->getAuthor() == $user))
      {
          $moderator = true;
      } else {
          $moderator = false;
      }

      // Lateral Wells info
      $defaultData = array();
      $formSearch = $this->createFormBuilder($defaultData)
              ->add('search', TextType::class, array(
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
			$wineTypesLabels = array_flip($formWine->get('type')->getConfig()->getOption('choices'));

      $regions = $em->getRepository('ROVBlogBundle:Region')->findBy(
              array(),
              array('name' => 'ASC')
          );
      $wineries = $em->getRepository('ROVBlogBundle:Winery')->findBy(
              array(),
              array('name' => 'ASC')
          );

      // Number of wine tasting reviews by month
      $now = new \DateTime();
      $year = $now->format('Y');
      $queryByMonth = $em->createQuery(
          'SELECT COUNT(w.id) as total, SUBSTRING(w.updated, 6, 2) as month, SUBSTRING(w.updated, 1, 4) as year
           FROM ROVBlogBundle:Wine w
           WHERE SUBSTRING(w.updated, 1, 4) >= :year
           AND w.published = :published
           GROUP BY month
           ORDER BY w.updated DESC');
      $queryByMonth->setParameter('year', ($year - 1));
      $queryByMonth->setParameter('published', true);
      $wineReviewsByMonth = $queryByMonth->getResult();

      return $this->render('ROVBlogBundle:Wines:wineReview.html.twig', array(
          'wine'              => $wine,
          'moderator'         => $moderator,
          'form_search'       => $formSearch->createView(),
          'wineTypesLabels'   => $wineTypesLabels,
          'wineReviewsMonth'  => $wineReviewsByMonth,
          'regions'           => $regions,
          'wineries'          => $wineries,
          'last_username'     => $helper->getLastUsername(),
          'new_region_form'   => $formNewRegion->createView(),
          'new_winery_form'   => $formNewWinery->createView(),
          'error'             => $error
      ));
  }

  /**
   * Preview a wine tasting review
   * @param  Request $request [description]
   * @param  [type]  $wine_id [description]
   * @return object           Twig template
   */
  public function previewWineAction(Request $request, $wine_id)
  {
      $session = $request->getSession();
      $user = $this->get('security.token_storage')->getToken()->getUser();
      $em = $this->getDoctrine()->getManager();
			// Get authentication utils
	  	$helper = $this->get('security.authentication_utils');

	    // Get the login error if there is any
	    $error = $helper->getLastAuthenticationError();

      // Wine review
      $query = $em->createQuery(
          'SELECT w, wry, r FROM ROVBlogBundle:Wine w
           JOIN w.winery wry
           JOIN wry.region r
           WHERE w.id = :wine_id'
          );
      $query->setParameter('wine_id', $wine_id);
      $wine = $query->getSingleResult();

      // Check whether the wine tasting review exists and it is already published
      if (!$wine)
      {
          $this->get('session')->getFlashBag()->add('error',
              'The wine tasting review does not exist or is not published yet'
          );

          return $this->redirect($this->generateUrl('rov_blog_wine_homepage'));
      }

      if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') || ($wine->getAuthor() == $user))
      {
          // Super user and author
          //////////////////////////////////////////////////////

          // Lateral Wells info
          $defaultData = array();
          $formSearch = $this->createFormBuilder($defaultData)
                  ->add('search', TextType::class, array(
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
					$wineTypesLabels = array_flip($formWine->get('type')->getConfig()->getOption('choices'));

          $regions = $em->getRepository('ROVBlogBundle:Region')->findBy(
                  array(),
                  array('name' => 'ASC')
              );
          $wineries = $em->getRepository('ROVBlogBundle:Winery')->findBy(
                  array(),
                  array('name' => 'ASC')
              );

          // Number of wine tasting reviews by month
          $now = new \DateTime();
          $year = $now->format('Y');
          $queryByMonth = $em->createQuery(
              'SELECT COUNT(w.id) as total, SUBSTRING(w.updated, 6, 2) as month, SUBSTRING(w.updated, 1, 4) as year
               FROM ROVBlogBundle:Wine w
               WHERE SUBSTRING(w.updated, 1, 4) >= :year
               AND w.published = :published
               GROUP BY month
               ORDER BY w.updated DESC');
          $queryByMonth->setParameter('year', ($year - 1));
          $queryByMonth->setParameter('published', true);
          $wineReviewsByMonth = $queryByMonth->getResult();

          return $this->render('ROVBlogBundle:Wines:winePreview.html.twig', array(
              'wine'              => $wine,
              'form_search'       => $formSearch->createView(),
              'wineTypesLabels'   => $wineTypesLabels,
              'wineReviewsMonth'  => $wineReviewsByMonth,
              'regions'           => $regions,
              'wineries'          => $wineries,
              'last_username'     => $helper->getLastUsername(),
              'new_region_form'   => $formNewRegion->createView(),
              'new_winery_form'   => $formNewWinery->createView(),
              'error'             => $error
          ));
      } else {
          // User is not the author
          $this->get('session')->getFlashBag()->add('error',
              'You are not allowed to preview the wine tasting review'
          );

          return $this->redirect($this->generateUrl('rov_blog_wine_homepage'));
      }
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
    $user = $this->get('security.token_storage')->getToken()->getUser();
		// Get authentication utils
  	$helper = $this->get('security.authentication_utils');

    // Get the login error if there is any
    $error = $helper->getLastAuthenticationError();

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
        'last_username'     => $helper->getLastUsername(),
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
    $user = $this->get('security.token_storage')->getToken()->getUser();
		// Get authentication utils
  	$helper = $this->get('security.authentication_utils');

    // Get the login error if there is any
    $error = $helper->getLastAuthenticationError();

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
        'last_username'     => $helper->getLastUsername(),
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
		// Get authentication utils
  	$helper = $this->get('security.authentication_utils');

    // Get the login error if there is any
    $error = $helper->getLastAuthenticationError();

    $region = new Region();
    $formNewRegion = $this->createForm(new RegionType(), $region);
    $winery = new Winery();
    $formNewWinery = $this->createForm(new WineryType(), $winery);

    if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
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
        $user = $this->get('security.token_storage')->getToken()->getUser();
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
        'last_username'         => $helper->getLastUsername(),
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
    $user = $this->get('security.token_storage')->getToken()->getUser();
		// Get authentication utils
  	$helper = $this->get('security.authentication_utils');

    // Get the login error if there is any
    $error = $helper->getLastAuthenticationError();

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
        'last_username'     => $helper->getLastUsername(),
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
    $user = $this->get('security.token_storage')->getToken()->getUser();
		// Get authentication utils
  	$helper = $this->get('security.authentication_utils');

    // Get the login error if there is any
    $error = $helper->getLastAuthenticationError();

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
        'last_username'     => $helper->getLastUsername(),
        'error'             => $error
    ));
  }

  /**
   * Delete wine tasting review
   * @param  Request $request [description]
   * @param  integer $wine_id [description]
   * @return object           Twig template
   */
  public function deleteWineReviewAction(Request $request, $wine_id)
  {
    $session = $request->getSession();
    $em = $this->getDoctrine()->getManager();
    $user = $this->get('security.token_storage')->getToken()->getUser();
		// Get authentication utils
  	$helper = $this->get('security.authentication_utils');

    // Get the login error if there is any
    $error = $helper->getLastAuthenticationError();

    if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
        $wine = $em->getRepository('ROVBlogBundle:Wine')->find($wine_id);
        $em->remove($wine);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success',
            'Wine tasting review deleted successfully'
        );
    }
    elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
        // Check whether the wine tasting review belongs to this user
        $wine = $em->getRepository('ROVBlogBundle:Wine')->findBy(array(
            'id' => $wine_id,
            'author' => $user
            ));

        if ($wine) {
            $em->remove($wine);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                'Wine tasting review deleted successfully'
            );
        } else {
            $this->get('session')->getFlashBag()->add('error',
                'You cannot delete this wine tasting review'
            );
        }

    } else {
        // User has not an admin role
        $this->get('session')->getFlashBag()->add('error',
            'You do not have enough privileges to delete wine tasting reviews'
        );
        return $this->redirect($this->generateUrl('rov_blog_wine_homepage'));
    }

    return $this->redirect($this->generateUrl('rov_blog_manage_wines'));
  }
}
