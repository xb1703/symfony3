<?php

namespace IKNSA\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function indexAction()
    {
        return $this->render('IKNSABlogBundle:Blog:index.html.twig');
    }

    public function listAction()
    {
        return $this->render('IKNSABlogBundle:Blog:list.html.twig');
    }
}
