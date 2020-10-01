<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
             ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'label' => 'Campus :',
                 'required' => false,
                 'placeholder' =>  "Choisir le campus"
            ])
            ->add('name',TextType::class, ['required' => false, 'label' => 'Nom : '])
            ->add('firstDate', DateType::class, [ 'required' => false, 'widget' => 'single_text',  'label' =>'Entre le :'])
            ->add('secondDate', DateType::class, [ 'required' => false, 'widget' => 'single_text',  'label' =>'et le :'])
            ->add('registeredOrNot', ChoiceType::class, ['choices' => [
                'Déjà inscrit' => 'registered',
                'Pas encore inscrit' => 'notRegistered'
            ],
                'multiple' => false,
                'required' => false,
                'expanded' => true,
                'placeholder' => false,
                'label' => false
            ])
            ->add('organizer', CheckBoxType::class,  ['label' => 'J\'organise','required' => false])
            ->add('over', CheckboxType::class,  ['label' => 'Sorties terminées', 'required' => false])
            ->add('search', SubmitType::class,  ['label' => 'Rechercher', 'attr' => ['class' => 'btn']]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
//            'data_class' => Event::class,
        ]);
    }
}
