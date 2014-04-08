<?php

namespace SweetTooth\Bundle\BindingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SweetToothBindingBundle:Default:index.html.twig', array('name' => $name));
    }
}
