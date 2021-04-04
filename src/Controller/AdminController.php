<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{
    public function includeHead(){
        $datenow = new \DateTime();
        $basededatos=$this->get('session')->get('em');
        return $this->render('Default/includes/head.html.twig', array('datenow'=>$datenow,"basededatos"=>$basededatos));
    }
    
    public function includeAsideLeft()
    {
        return $this->render('Default/includes/aside_left.html.twig', array());
    }
}