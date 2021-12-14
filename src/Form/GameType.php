<?php


namespace App\Form;


use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    /**
     * @inheritDoc
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", TextType::class, [
                "label" => "Nom : ",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ])
                ]
            ])
            ->add("link", TextType::class, [
                "label" => "Lien de téléchargement : ",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ])
                ]
            ])
            ->add("description", TextType::class, [
                "label" => "Description : "
            ])
            ->add("category", ChoiceType::class, [
                "label" => "Catégorie : ",
                "choices" => [
                    "R-18 Games" => "R-18 Games",
                    "Familial" => "Familial",
                    "Science-Fiction" => "Science-Fiction",
                    "Historique" => "Historique",
                    "Horreur" => "Horreur"
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ])
                ]
            ]);
    }

    /**
     * @inheritDoc
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("dataClass", Game::class);
    }

}