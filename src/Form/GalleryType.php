<?php

namespace App\Form;

use App\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['required' => true, 'label' => 'Nom'])
            ->add('background', FileType::class, ['mapped' => false, 'required' => false, 'label' => 'Image de couverture', 'constraints' => [
                new File([
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg',

                    ],
                    'mimeTypesMessage' => 'Séléctionner un document PNG ou JPEG valide',
                ])
            ]])
            ->add('ordering', NumberType::class, ['required' => false, 'label' => 'Ordre'])
            ->add('backgroundXOffset', NumberType::class, ['required' => true, 'label' => 'Décalage horizontal', 'attr' => ['min' => -100, 'max' => 100], 'data' => 0])
            ->add('backgroundYOffset', NumberType::class, ['required' => true, 'label' => 'Décalage vertical', 'attr' => ['min' => -100, 'max' => 100], 'data' => -50]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
}
