<?php

namespace App\Cart;

use App\Entity\Cart;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class CartService
{

    protected $shipping = 4.5;

    protected $session;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    

    public function getShipping()
    {
        return $this->shipping;
    }


    public function add(Product $product)
    {
        // Le but est d'ajouter un produit dans le panier (nécessite l'accès à la session)

        // Existe-t-il un panier dans la session ?
        if (!$this->session->has('cart-items')) {
            // Si non : je créé un panier vide dans la session 
            // C'est un tableau vide qui portera le nom "cart-items"
            $this->session->set('cart-items', new Cart());
        }

        // Je récupère le tableau qui porte le nom "cart-items" dans ma session
        // $items = $session->get('cart-items');
        //$session->set('cart-items', new Cart());

        // Je récupère le tableau qui porte le nom "cart-items" dans ma session
        $cart = $this->session->get('cart-items');

        //  Avant la class cartItem
        // // Je pars du principe que le produit n'est pas encore dans le panier
        // $quantity = 0;

        // // Par contre, si il est déjà dans le panier
        // if (!empty($items[$product->getId()])) {
        //     // Je prend la quantité déjà présente en référence
        //     $quantity = $items[$product->getId()]['quantity'];
        // }

        // // J'ajoute un couple produit, quantity dans le tableau
        // $items[$product->getId()] = [
        //     "product" => $product,
        //     // Je met à jour la quantité en ajoutant 1
        //     "quantity" => $quantity + 1
        // ];

        /*********************************************************** */
        // // Si ce produit n'existe pas dans mon panier
        // if (empty($items[$product->getId()])) {
        //     // Je créé le couple produit:quantity
        //     $items[$product->getId()] = new CartItem($product, 1);
        // } else {
        //     // Sinon, je ne fais qu'augmenter de 1 la quantity
        //     $items[$product->getId()]->increment();

        // Si ce produit n'existe pas dans mon panier
        if ($cart->contains($product->getId()) === false) {
            // Je créé le couple produit:quantity
            $cart->add($product, 1);
        } else {
            // Sinon, je ne fais qu'augmenter de 1 la quantity
            $cart->get($product->getId())->increment();
        }
        // }
        /**************************************************************** */

        // Je remet mon tableau dans la session
        // $session->set('cart-items', $items);
        $this->session->set('cart-items', $cart);

       // dd($this->session->get("cart-items"));
    }



    public function remove(Product $product)
    {
        // Le but est de supprimer un produit du panier  (nécessite l'accès à la session)
        if (!$this->session->has('cart-items')) {
            return false;
        }

        // Je récupère ma liste de couples produit:quantity
        //$items = $session->get('cart-items');
        $cart = $this->session->get('cart-items');

        // Je supprime l'élément dont la clé est l'id du produit
        //unset($items[$product->getId()]);
        $cart->remove($product->getId());

        // Je remet mon panier dans la session
        //$session->set('cart-items', $items);
        $this->session->set('cart-items', $cart);

        return true;
        //dd($this->session->get('cart-items'));
    }


    public function empty()
    {
        // Le but est de vider le panier (nécessite l'accès à la session)
        $this->session->remove('cart-items');
       // dd($session);
    }


    
    /**
     * Récupère la liste des CartItem qui sont dans la session
     *
     * @return CartItem[]
     */
    public function getItems(): array
    {
        $cart = $this->session->get('cart-items', new Cart());

        return $cart->all();
    }



    /**
     * Récupère le total du Cart stocké en session
     *
     * @return float
     */
    public function getTotal(): float
    {
        $cart = $this->session->get('cart-items', new Cart());

        return $cart->getTotal();
    }


    /**
     * Récupère le total du panier + la livraison
     *
     * @return float
     */
    public function getGrandTotal(): float
    {
        return $this->shipping + $this->getTotal();
    }


}