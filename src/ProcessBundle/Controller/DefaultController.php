<?php

namespace ProcessBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ProcessBundle:Default:index.html.twig');
    }
}
