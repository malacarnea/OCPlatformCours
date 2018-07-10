<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class CoreController extends Controller
{
    public function indexAction() {
       
        return $this->render('OCCoreBundle:Core:index.html.twig');
    }
    
    public function contactAction(Request $request){
        $request->getSession()->getFlashBag()->add('info', "Pas de page de contact pour le moment, merci de patienter jusqu'à ce que je sache faire un formulaire.");
        return $this->redirectToRoute('oc_core_home');
    }
}
