<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TodoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, ['choices' => $options['categories'], 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:5px']])
            ->add('name', TextType::class, ['label' => 'Title', 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('priority', ChoiceType::class, ['choices' => $options['priorities'], 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])
            ->add('date_due', DateTimeType::class, ['attr' => ['class' => '', 'style' => 'margin-bottom:15px']])
            ->add('save', SubmitType::class, ['label' => $options['submit'], 'attr' => ['class' => 'btn btn-primary']]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Todo',
            'categories' => null,
            'priorities' => null,
            'submit' => null,
        ));
    }
}