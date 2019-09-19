<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product_index")
     */
    public function index(ProductRepository $repo)
    {

        $products = $repo->findAll();

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            "products" => $products
        ]);
    }



    /**
     * @Route("/product/create", name="product_create")
     */
    public function create(Request $request, ObjectManager $manager)
    {

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($product);
            $manager->flush();

            
            return $this->redirectToRoute('product_index');
        }
       

        return $this->render('product/create.html.twig', [
            'controller_name' => 'ProductController',
            "form" => $form->createView()
        ]);
    }


    /**
     * @Route("/product/edit/{id}", name="product_edit")
     */
    public function edit(Request $request, ObjectManager $manager ,Product $product)
    {

        //$product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($product);
            $manager->flush();


            return $this->redirectToRoute('product_index');
        }


        return $this->render('product/edit.html.twig', [
            'controller_name' => 'ProductController',
            "form" => $form->createView()
        ]);
    }


    /**
     * @Route("/product/delete:{id}", name="product_delete")
     */
    public function delete(ObjectManager $manager, Product $product)
    {

       $manager->remove($product);
       $manager->flush();


        return $this->redirectToRoute('product_index');
        


        return $this->render('product/edit.html.twig', [
            'controller_name' => 'ProductController'
        
        ]);
    }

}
