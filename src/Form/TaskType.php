<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Tag;
use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose an option',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'dataDropdownItem' => 'tag',
                ]
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose authors',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'data-dropdown-item' => 'authors',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'отправить',
                'attr' => [
                    'class' => 'button'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Task::class,
        ));
    }
}