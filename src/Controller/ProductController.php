<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\MarkdownCacheHelper;
use cebe\markdown\Markdown;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
     * @Route("/product/delete/{id}", name="product_delete")
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





    /**
     * @Route("/product/{id}", name="product_show")
     */
    public function show(Product $product, MarkdownCacheHelper $helper)
    {

        // $introduction = $product->getIntroduction();
        // $key = md5($introduction);
        // $key2 = md5($introduction);
        // $key3 = md5($introduction. "k");

        // dd($key, $key2, $key3);

        // si 'introduction n'est pas trouvé, la function est appellé
        // $cachedIntroduction = $cache->get(md5($product->getIntroduction()), function(ItemInterface $item) use ($parser, $product) {
        //     return $parser->parse($product->getIntroduction());
        // });

        $cachedIntroduction = $helper->parse($product->getIntroduction());
        $cachedDescription = $helper->parse($product->getDescription());

        



        return $this->render('product/show.html.twig', [
            'controller_name' => 'ProductController',
            "product" => $product,
            "introduction" => $cachedIntroduction,
            "description" => $cachedDescription

        ]);
    }



    /**
     * @Route("/search", name="product_search")
     */
    public function search(Request $request, ProductRepository $repo)
    {

        $search = $request->query->get('search', null);

        if($search)
        {
            $result = $repo->findBySearch($search);
            //dd($result);
        }

        return $this->render("search/search.html.twig", [
            "result" => $result,
            "search" => $search
        ]);
       
    }

}
