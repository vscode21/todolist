<?php
/**
 * Created by PhpStorm.
 * User: 21G
 * Date: 7/22/2016
 * Time: 1:54 PM
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $authUtils = $this->get('security.authentication_utils');

        //get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        //last user name entered by the user
        $lastUserName = $authUtils->getLastUserName();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUserName,
            'error' => $error,
        ]);
    }
    
}