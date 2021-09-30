<?php
/**
 * Created by PhpStorm.
 * User: Emanuel
 * Date: 17.09.2019
 * Time: 20:29
 */

namespace App\Form\Type;


use App\Entity\AuditTomAbteilung;
use App\Entity\Rooms;
use App\Entity\Standort;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('standort', EntityType::class, [
                'choice_label' => 'name',
                'class' => Standort::class,
                'choices' => $options['standort'],
                'label' => 'label.serverKonferenz',
                'translation_domain' => 'form',
                'multiple' => false,
                'required' => true,
                'attr' => array('class' => 'moreFeatures')
            ])
            ->add('name', TextType::class, ['required' => true, 'label' => 'label.konferenzName', 'translation_domain' => 'form'])
            ->add('agenda', TextareaType::class, ['required' => false, 'label' => 'label.agenda', 'translation_domain' => 'form'])
            ->add('start', DateTimeType::class, ['attr' => ['class' => 'flatpickr'], 'label' => 'label.start', 'translation_domain' => 'form', 'widget' => 'single_text'])
            ->add('duration', ChoiceType::class, [
                'label' => 'label.dauerKonferenz',
                'translation_domain' => 'form',
                'choices' => [
                    'option.10min' => 10,
                    'option.15min' => 15,
                    'option.30min' => 30,
                    'option.45min' => 45,
                    'option.60min' => 60,
                    'option.90min' => 90,
                    'option.120min' => 120,
                    'option.150min' => 150,
                    'option.180min' => 180,
                    'option.210min' => 210,
                    'option.240min' => 240,
                    'option.270min' => 270,
                    'option.300min' => 300,
                    'option.330min' => 330,
                    'option.360min' => 360,
                    'option.390min' => 390,
                    'option.420min' => 420,
                    'option.450min' => 450,
                    'option.480min' => 480,
                ]
            ])
            ->add('entryDateTime', DateTimeType::class, ['required'=>false, 'attr' => ['class' => 'flatpickr'], 'label' => 'label.entryDateTime', 'translation_domain' => 'form', 'widget' => 'single_text'])
            ->add('promoter', TextType::class, ['required' => false, 'label' => 'label.promoter', 'translation_domain' => 'form'])
            ->add('additionalInfo', TextareaType::class, ['required' => false, 'label' => 'label.additionalInfo', 'translation_domain' => 'form'])
            ->add('scheduleMeeting', CheckboxType::class, array('required' => false, 'label' => 'label.scheduleMeeting', 'translation_domain' => 'form'))
            ->add('public', CheckboxType::class, array('required' => false, 'label' => 'label.puplicRoom', 'translation_domain' => 'form'))
            ->add('showRoomOnJoinpage', CheckboxType::class, array('required' => false, 'label' => 'label.showRoomOnJoinpage', 'translation_domain' => 'form'))
            ->add('showRoomOnCalendar', CheckboxType::class, array('required' => false, 'label' => 'label.showRoomOnCalendar', 'translation_domain' => 'form'))
            ->add('maxParticipants', NumberType::class, array('required' => false, 'label' => 'label.maxParticipants', 'translation_domain' => 'form', 'attr' => array('placeholder' => 'placeholder.maxParticipants')))
            ->add('textWhenNoSpace', TextType::class, array('required' => false, 'label' => 'label.textWhenNoSpace', 'translation_domain' => 'form', 'attr' => array('placeholder' => 'placeholder.textWhenNoSpace')))
            ->add('textWhenRoomWarteliste', TextType::class, array('required' => false, 'label' => 'label.textWhenRoomWarteliste', 'translation_domain' => 'form', 'attr' => array('placeholder' => 'placeholder.textWhenNoSpace')))

            ->add('waitinglist', CheckboxType::class, array('required' => false, 'label' => 'label.waitinglist', 'translation_domain' => 'form'))
            ->add('maxWaitingList', NumberType::class, array('required' => false, 'label' => 'label.maxWaitingList', 'translation_domain' => 'form', 'attr' => array('placeholder' => 'placeholder.maxParticipants')))
            ->add('allowGroups', CheckboxType::class, array('required' => false, 'label' => 'label.allowGroups', 'translation_domain' => 'form'))
            ->add('maxGroupSize', NumberType::class, array('required' => false, 'label' => 'label.maxGroupSize', 'translation_domain' => 'form', 'attr' => array('placeholder' => 'placeholder.maxParticipants')))
            ->add('showInCalendarWhenNoSpace', CheckboxType::class, array('required' => false, 'label' => 'label.showInCalendarWhenNoSpace', 'translation_domain' => 'form'))
            ->add('silentMode', CheckboxType::class, array(  "mapped" => false, 'required' => false, 'label' => 'label.silentMode', 'translation_domain' => 'form'))
            ->add('freeFields', CollectionType::class,
                ['entry_type' => FreeFieldType::class,
                    'entry_options' => ['label' => 'false',],
                    'allow_add' => true,
                    'allow_delete' =>true,
                    'by_reference' => false,
                    'label' => false,
                    'translation_domain' => 'form',])
            ->add('submit', SubmitType::class, ['attr' => array('class' => 'btn btn-outline-primary'), 'label' => 'label.speichern', 'translation_domain' => 'form']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'standort' => array(),
            'data_class' => Rooms::class
        ]);

    }
}
