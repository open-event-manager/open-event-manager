<?php
/**
 * Created by PhpStorm.
 * User: Emanuel
 * Date: 17.09.2019
 * Time: 20:29
 */

namespace App\Form\Type;


use App\Entity\AuditTomAbteilung;
use App\Entity\Standort;
use League\CommonMark\Inline\Element\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreaterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('amount', NumberType::class, ['required' => true, 'label' => 'label.amount', 'translation_domain' => 'form', ])
            ->add('distance', NumberType::class, ['required' => true, 'label' => 'label.distance', 'translation_domain' => 'form', ])
            ->add('unit', ChoiceType::class, ['required' => true, 'label' => 'label.unit', 'translation_domain' => 'form', 'choices'=>
                array('choice.min'=>'min','choice.hour'=>'hour','choice.day'=>'day','choice.week'=>'weeks','choice.month'=>'months','choice.year'=>'years')])
            ->add('addUsers',CheckboxType::class,['required' => false, 'label' => 'label.addUsers', 'translation_domain' => 'form', ])
            ->add('submit', SubmitType::class, ['attr' => array('class' => 'btn btn-outline-primary'), 'label' => 'label.speichern', 'translation_domain' => 'form']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);

    }
}
