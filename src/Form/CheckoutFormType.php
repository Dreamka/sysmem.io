<?php

namespace App\Form;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class CheckoutFormType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $items = $this->entityManager->getRepository(Item::class)->findAll();

        $choices = [];
        foreach ($items as $item) {
            $choices[$item->getName()] = $item->getId();
        }

        $builder
            ->add('item_id', ChoiceType::class, [
                'choices'   => $choices,
                'label'     => "Выберите товар"
            ])
            ->add("tax_number", TextType::class, [
                "label"     => "TAX-номер",
                'constraints' => [
                    new Regex([
                        "pattern"   => "/[A-Z]{2}[\d]{10,12}/",
                        "message"   => "Введите корректный TAX-номер"
                    ])
                ]
            ])
            ->add("submit", SubmitType::class, [
                "label"     => "Рассчитать"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
