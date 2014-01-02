<?php

namespace ROV\StartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SiteController extends Controller
{
    public function staticAction($page)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // Get the login error if there is any
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        return $this->render('ROVStartBundle:Site:'.$page.'.html.twig',
        	array(
        		'page' => $page,
        		'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error
        	)
        );
    }
}