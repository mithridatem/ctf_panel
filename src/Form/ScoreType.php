<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Score;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('participant', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'login',
                'attr' => ['class' => 'py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600',],
                'label' => 'Choisir votre login dans la liste',
                'label_attr' => [
                    'class' => 'block text-sm mb-2 dark:text-white',
                ]
            ])
            ->add('Flag', TextType::class, [
                'attr' => [
                    'class' => 'py-3 px-4 block w-full border-gray-600 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600',
                ],
                'mapped' => false,
                'label' => 'Entrer le code',
                'label_attr' => [
                    'class' => 'block text-sm mb-2 dark:text-white'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Score::class,
        ]);
    }
}
