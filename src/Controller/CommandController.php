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
    public function payment(CartService $cartService, SessionInterface $session)
    {
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
