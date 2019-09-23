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
     * @Route("/cart", name="cart_index")
     */
    public function index(CartService $cartService)
    {
        $items = $cartService->getItems();
        $total = $cartService->getTotal();
        $shipping = $cartService->getShipping();
        $grandTotal = $cartService->getGrandTotal();


        return $this->render('cart/index.html.twig', [
            "items" => $items,
            "total" => $total,
            "shipping" => $shipping,
            "grandTotal" => $grandTotal
        ]);
    }



    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add(Product $product, CartService $cartService)
    {
        $cartService->add($product);

        $this->addFlash(
            'success',
            "Le produit <strong>{$product->getTitle()}</strong> a bien été ajouté au panier !"
        );

        

        return $this->redirectToRoute("cart_index");

        
    }


    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove(Product $product, CartService $cartService)
    {
            $cartService->remove($product);

        $this->addFlash(
            'success',
            "Le produit <strong>{$product->getTitle()}</strong> a bien été supprimé du panier !"
        );

            return $this->redirectToRoute("cart_index");
    }


    /**
     * @Route("/cart/empty", name="cart_empty")
     */
    public function empty(CartService $cartService)
    {
        // Le but est de vider le panier (nécessite l'accès à la session)
        $cartService->empty();

        $this->addFlash(
            'success',
            "Le panier a bien été vidé!"
        );

        return $this->redirectToRoute("home");
    }




    public function counter(CartService $cartService)
    {

        $total = 0;

        foreach($cartService->getItems() as $item)
        {
            $total += $item->getQuantity();
        }
       
        return $this->render('cart/counter.html.twig', [
            "total" => $total
        ]);
    }

}
