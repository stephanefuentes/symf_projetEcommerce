<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextareaType::class, [
                'label' => 'Votre adresse de livraison',
                'attr' => [
                    'placeholder' => "Adresse de livraison coquinou .."
                ],
                'constraints' => [
                    new NotBlank(['message' => "l'adresse est obligatoire .."]),
                    new Length(['min' => 10, 'minMessage' => "l'adresse doit avoir au moins 10 caractères mon coquin ..."])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
