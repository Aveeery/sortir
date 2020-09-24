<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Place;
use Faker\Provider\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Form;


class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('city', EntityType::class, [
                'class' => City::class,
                'mapped' => false
            ])
            ->add('place', null, [
                'label' => 'lieu',
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner le lieu',
                'required' => false,
            ])
            ->add('name')
            ->add('startDate', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('duration')
            ->add('closingDate', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('description')
            ->add('maxAttendees')
            ->add('publishEvent', SubmitType::class,  ['label' => 'Publier'])
            ->add('stashEvent', SubmitType::class,  ['label' => 'Enregistrer'])
            ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
