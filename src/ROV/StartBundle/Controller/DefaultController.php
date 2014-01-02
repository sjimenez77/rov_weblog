<?php

namespace ROV\StartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function homeAction()
    {
        return $this->render('ROVStartBundle:Default:index.html.twig');
    }
}
