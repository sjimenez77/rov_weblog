<?php

namespace ROV\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function profileAction()
    {
        return $this->render('ROVUsersBundle:Default:profile.html.twig');
    }

    /**
     * Login form
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $session->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        return $this->render('ROVUsersBundle:Default:login.html.twig', 
            array(
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error
            )
        );
    }
}
