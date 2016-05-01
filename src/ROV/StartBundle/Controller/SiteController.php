<?php

namespace ROV\StartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{
    public function staticAction($page)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // Get authentication utils
        $helper = $this->get('security.authentication_utils');

        // Get the login error if there is any
        $error = $helper->getLastAuthenticationError();

        return $this->render('ROVStartBundle:Site:'.$page.'.html.twig',
        	array(
        		'page' => $page,
        		'last_username' => $helper->getLastUsername(),
            'error'         => $error
        	)
        );
    }
}
