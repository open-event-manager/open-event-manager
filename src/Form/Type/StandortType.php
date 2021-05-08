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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StandortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, ['required' => true, 'label' => 'label.standort.name', 'translation_domain' => 'form', ])
            ->add('street', TextType::class, ['required' => false, 'label' => 'label.standort.street', 'translation_domain' => 'form', ])
            ->add('number', TextType::class, ['required' => false, 'label' => 'label.standort.number', 'translation_domain' => 'form', ])
            ->add('plz', TextType::class, ['required' => true, 'label' => 'label.standort.plz', 'translation_domain' => 'form', ])
            ->add('city', TextType::class, ['required' => true, 'label' => 'label.standort.city', 'translation_domain' => 'form',])
            ->add('directions', TextareaType::class, ['required' => false, 'label' => 'label.standort.directions', 'translation_domain' => 'form', ])
            ->add('licenseKey', TextType::class, ['required' => false, 'label' => 'label.serverLicenseKey', 'translation_domain' => 'form'])
            ->add('submit', SubmitType::class, ['attr' => array('class' => 'btn btn-outline-primary'), 'label' => 'label.speichern', 'translation_domain' => 'form']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);

    }
}
