<?php

namespace App\Form;

use App\Entity\Gallery;
use App\Entity\Photo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['required' => true, "label" => "Nom de la photo"])
            ->add('img', FileType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('description', TextareaType::class, ['required' => false, "label" => "Description"])
            ->add('location', TextType::class,  ['required' => false, "label" => "Lieu"])
            ->add('datePhoto', DateType::class,  ['required' => false, "label" => "Date de la photo"])
            ->add('gallery', EntityType::class, [
                'class' => Gallery::class,
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
