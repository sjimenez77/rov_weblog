<?php
// src/ROV/UserBundle/Listener/LoginListener.php
namespace ROV\UsersBundle\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginListener
{
	// We can add more fields to this class ...
	private $context, $router, $username, $locale = null;

	public function __construct(AuthorizationChecker $context, Router $router)
    {
        $this->context = $context;
        $this->router = $router;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        // Here we can obtain any information about the user who has just logged in ...
        // $this->field = $token->getUser()-> ...;
        $this->username = $token->getUser()->getUsername();
        $this->locale = $token->getUser()->getLocale();
    }

	public function onKernelResponse(FilterResponseEvent $event)
    {
        // We can redirect the response where we wish depending of the user role
        if (null != $this->username)
        {
            if ($this->context->isGranted('ROLE_ADMIN'))
            {
                // Change this to admin home route if necessary
                $route = $this->router->generate('home', array(
                    '_locale' => $this->locale
                ));
            }
            elseif ($this->context->isGranted('ROLE_MODERATOR') || $this->context->isGranted('ROLE_USER'))
            {
                $route = $this->router->generate('home', array(
                    '_locale' => $this->locale
                ));
            }

            $event->setResponse(new RedirectResponse($route));
            $event->stopPropagation();
        }
    }

}
