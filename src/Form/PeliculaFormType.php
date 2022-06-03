<?php

namespace App\Form;

use App\Entity\Pelicula;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


class PeliculaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo')
            ->add('duracion')
            ->add('genero')
            ->add('director')
            ->add('sinopsis', TextareaType::class, ['attr' => ['class' => 'tinymce']])
            ->add('estreno')
            ->add('valoracion', RangeType::class, ['attr' => ['min' => 1,'max' => 5]])
            ->add('caratula', FileType::class, [
                'label'=>'Carátula (jpg, png o gif):', 
                'required' => false, 
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '2048K',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Solamente se pertmite jpg, png o gif'
                    ])
                ]
            ])
            ->add('Guardar', SubmitType::class, ['attr' => ['class' => 'btn btn-success my-3']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pelicula::class,
        ]);
    }
}
