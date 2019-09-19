<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    
    /**
     * @Route("/", name="home")
     */
    public function index(CategoryRepository $repo)
    {

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            "categories" => $repo->findAll()
        ]);
    }


}
