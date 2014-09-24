<?php

namespace ROV\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

use ROV\BlogBundle\Entity\Article;
use ROV\BlogBundle\Entity\Comment;
use ROV\BlogBundle\Entity\Tag;
use ROV\BlogBundle\Entity\Category;
use ROV\BlogBundle\Form\Backend\ArticleType;
use ROV\BlogBundle\Form\Backend\CategoryType;
use ROV\BlogBundle\Form\Backend\TagType;
use ROV\BlogBundle\Form\Frontend\CommentType;

use ROV\BlogBundle\Util\Util;

class WineController extends Controller
{
	public function newZoneAction(Request $request)
	{
		# code...
	}

	public function newWineryAction(Request $request)
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

	public function manageWinesAction(Request $request)
	{
		# code...
	}

}
