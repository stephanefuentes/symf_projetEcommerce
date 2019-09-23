<?php

namespace App\Controller;

use DateTime;
use App\Entity\Command;
use App\Cart\CartService;
use App\Form\AddressType;
use App\Entity\CommandProduct;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandController extends AbstractController
{

    /**
     * @Route("/command", name="command_index")
     */
    public function index(Request $request, SessionInterface $session)
    {

        $form = $this->createForm(AddressType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $session->set('command-address', $data['address']);
            //dd($session);

            return $this->redirectToRoute("command_payment");

            
        }



        return $this->render('command/index.html.twig', [
            'controller_name' => 'CommandController',
            "form" => $form->createView()
        ]);
    }



    /**
     * @Route("/payment", name="command_payment")
     */
    public function payment(CartService $cartService, SessionInterface $session, Request $request)
    {

        if($request->request->get('stripeToken'))
        {
            // création du paiement
            // code récupérer sur le site Stripe
            // Set your secret key: remember to change this to your live secret key in production
            // See your keys here: https://dashboard.stripe.com/account/apikeys
            \Stripe\Stripe::setApiKey('sk_test_R7lEkr1O6MHswomq4mebOXOR004FrEZQkj');

            // Token is created using Checkout or Elements!
            // Get the payment token ID submitted by the form:
            //$token = $_POST['stripeToken'];
            $token = $request->request->get('stripeToken');

            $charge = \Stripe\Charge::create([
                'amount' => $cartService->getGrandTotal()*100,
                'currency' => 'eur',
                'description' => 'Example charge',
                'source' => $token,
            ]);

            if($charge->status === "succeeded")
            {
                return $this->redirectToRoute("command_process");
            }

        }

       

        return $this->render('command/payment.html.twig', [
            'total' => $cartService->getGrandTotal(),
            'address' => $session->get('command-address')
        ]);
    }



    /**
     * @Route("/process", name="command_process")
     */
    public function process(SessionInterface $session, CartService $cartService, ObjectManager $manager, ProductRepository $repo)
    {

        // 1. Je créé une commande avec sa date et son adresse
        $commande = new Command();
        $commande->setCreatedAt(new DateTime())
            ->setAddress($session->get('command-address'));

        $manager->persist($commande);

        // 2. Je créé les CommandProduct
        foreach ($cartService->getItems() as $item) {
            $commandProduct = new CommandProduct();

            // PATCH LE PLUS CON :
            $product = $repo->find($item->getProduct()->getId());

            $commandProduct->setProduct($product)
                ->setQuantity($item->getQuantity())
                ->setCommand($commande);

            $manager->persist($commandProduct);
        }

        $manager->flush();

        $cartService->empty();

        return $this->render('command/success.html.twig', [
            'commande' => $commande
        ]);
    }

}
