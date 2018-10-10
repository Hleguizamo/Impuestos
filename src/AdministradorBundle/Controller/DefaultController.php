<?php

namespace AdministradorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function loginAction(Request $request){
  
        $session = $request->getSession();

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('AdministradorBundle:Default:login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'provBloqueado' => null
        ));
    }
}
