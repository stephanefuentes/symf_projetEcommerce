<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\CartItem;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add(Product $product, CartService $cartService)
    {
        $cartService->add($product);
        dd($cartService->getItems());

        
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove(Product $product, CartService $cartService)
    {
            $cartService->remove($product);
            dd($cartService->getItems());
    }


    /**
     * @Route("/cart/empty", name="cart_empty")
     */
    public function empty(CartService $cartService)
    {
        // Le but est de vider le panier (nécessite l'accès à la session)
        $cartService->empty();
        dd($cartService->getItems());
    }

}
