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
		$numberPosts = 3;

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
        	 ORDER BY w.updated ASC'
        	);
        $query->setParameter('published', true);
        $query->setMaxResults($numberPosts);
        $query->setFirstResult(($page-1) * $numberPosts);
        $lastWineReviews = $query->getResult();

        // Remaining wine tasting reviews
        $queryCount = $em->createQuery('SELECT a FROM ROVBlogBundle:Wine a WHERE a.published = :published');
        $queryCount->setParameter('published', true);
        $queryCount->setFirstResult(($page) * $numberPosts);
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

	public function wineReviewsRegionAction(Request $request, $slug, $page)
	{
		# code...
	}

	public function wineReviewsWineryAction(Request $request, $slug, $page)
	{
		# code...
	}

	public function wineReviewsDateAction(Request $request, $year, $month, $page)
	{
		# code...
	}

    public function previewWineAction(Request $request, $wine_id)
    {
        # code...
    }

	public function newWineAction(Request $request)
	{
		# code...
	}

	public function editWineAction(Request $request)
	{
		# code...
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
            $wineReviews = $em->getRepository('ROVBlogBundle:Wine')->findBy(
                array(),
                array('updated' => 'DESC')
            );
        }
        else
        {
            // Get the wine tasting reviews from the author $user
            $user = $this->get('security.context')->getToken()->getUser();
            $wineReviews = $em->getRepository('ROVBlogBundle:Wine')->findBy(
                array('author' => $user),
                array('updated' => 'DESC')
            );
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
}
