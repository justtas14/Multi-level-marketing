<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $user = $this->getUser();
        /**
         * @var User $user
         */
        if (!$user){
            $this->redirectToRoute('login');
        }
        elseif (in_array('ROLE_ADMIN', $user->getRoles())){
            $this->redirectToRoute('admin');
        }else{
            $this->redirectToRoute('associate');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
