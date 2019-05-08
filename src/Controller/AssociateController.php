<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AssociateController extends AbstractController
{
    /**
     * @Route("/associate", name="associate")
     */
    public function index()
    {
        return $this->render('associate/index.html.twig', [
            'controller_name' => 'AssociateController',
        ]);
    }
}
