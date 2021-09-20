<?php
namespace App\Form\Type;


use App\Entity\FreeField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FreeFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('label', TextType::class, ['required' => true, 'attr' => ['class' => 'd-inline w-100','placeholder'=>'label.freefieldName'],'translation_domain' => 'form'])
            ->add('required', CheckboxType::class, ['required' => false, 'label'=>'label.required','translation_domain' => 'form']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FreeField::class,
        ]);

    }
}
