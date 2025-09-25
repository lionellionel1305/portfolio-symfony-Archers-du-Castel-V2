<?php
// src/Form/PopupSettingsType.php
namespace App\Form;

use App\Entity\PopupSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PopupSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    ->add('enabled', CheckboxType::class, [
        'label' => 'Activer le popup',
        'required' => false,
    ])
    ->add('content', TextareaType::class, [
        'label' => 'Contenu du popup',
        'required' => false,
    ])
    ->add('fontSize', TextType::class, [
        'label' => 'Taille du texte (ex: 1rem, 16px)',
        'required' => false,
    ])
    ->add('fontColor', ColorType::class, [
        'label' => 'Couleur du texte',
        'required' => false,
    ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PopupSettings::class,
        ]);
    }
}
