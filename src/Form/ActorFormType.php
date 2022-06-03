<?php

namespace App\Form;

use App\Entity\Actor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class ActorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            // ->add('fecha_nacimiento')
            ->add('fecha_nacimiento', DateType::class, [
                    'widget' => 'single_text',
                    // 'widget' => 'choice',
                    // 'years' => range(date('Y'), date('Y')+100),
                    'months' => range(date('m'), 12),
                    'days' => range(date('d'), 31),
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
            ->add('nacionalidad')
            ->add('biografia', TextareaType::class, ['attr' => ['class' => 'tinymce']])
            ->add('Guardar', SubmitType::class, ['attr' => ['class' => 'btn btn-success my-3']])
            

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Actor::class,
        ]);
    }
}
