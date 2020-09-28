<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterEventType extends AbstractType
{

    //Formulaire de filtres de recherche d'event
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', TextType::class, ['mapped' => false, 'required' => false])
            ->add('name',TextType::class, ['required' => false])
            ->add('firstDate', DateType::class, [ 'required' => false, 'widget' => 'single_text'])
            ->add('secondDate', DateType::class, [ 'required' => false, 'widget' => 'single_text'])
            ->add('registeredOrNot', ChoiceType::class, ['choices' => [
                'Sorties auxquelles je suis inscrit' => 'registered',
                'Sorties auxquelles je ne suis pas inscrit' => 'notRegistered'
            ],
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'expanded' => true,
                'placeholder' => false,
                'label' => false
            ])
            ->add('organizer', CheckBoxType::class,  ['label' => 'J\'organise', 'mapped' => false, 'required' => false])
            ->add('over', CheckboxType::class,  ['label' => 'Sorties terminées', 'mapped' => false, 'required' => false])
            ->add('search', SubmitType::class,  ['label' => 'Rechercher'])
        ->setMapped(false);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => Event::class,
        ]);
    }
}
