<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\CreateItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/item', name: 'app_item')]
    public function index(): Response
    {
        $items = $this->entityManager->getRepository(Item::class)->findAll();

        return $this->render('item/index.html.twig', [
            'items'             => $items

        ]);
    }

    #[Route('/item/create', name: 'create_item')]
    public function createItem(Request $request)
    {
        $item = new Item();
        $message = NULL;

        $form = $this->createForm(CreateItemType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();


            $this->entityManager->persist($item);
            $this->entityManager->flush();
            $message = "Товар %name% создан";
        }

        return $this->render('item/create.html.twig', [
            'form'              => $form,
            'item'              => $item,
            'message'           => $message
        ]);
    }

    #[Route('/item/{id}', 'show_item')]
    public function show($id): Response
    {
        $item = $this->entityManager->getRepository(Item::class)->find($id);
        dump($item);

        return $this->render("item/show.html.twig", [
            "item"          => $item
        ]);
    }
}
