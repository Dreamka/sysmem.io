<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\CheckoutFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    // Данные можно и нужно хранить в БД, но это и не боевая задача.
    private $countries = [
        "DX"  => [
            "tax"   => 19,
            "label" => "Германия"
        ],
        "IT"  => [
            "tax"   => 22,
            "label" => "Италия"
        ],
        "GR"  => [
            "tax"   => 24,
            "label" => "Греция"
        ],
    ];

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
    #[Route('/checkout', name: 'app_checkout')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(CheckoutFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData();
            $item = $this->entityManager->getRepository(Item::class)->find($form_data["item_id"]);
            $user_country = $this->countries[mb_substr($form_data['tax_number'], 0, 2)];
            $tax = ( $item->getPrice() / 100 ) * $user_country["tax"];
            $full_price = $item->getPrice() + $tax;

            return $this->render('checkout/index.html.twig', [
                'form'              => $form,
                'item'              => $item,
                'tax'               => $tax,
                'full_price'        => $full_price,
                'user_country'      => $user_country
            ]);
        } else {
            $errors = $form->getErrors();
        }

        return $this->render('checkout/index.html.twig', [
            'form'              => $form,
        ]);
    }
}
