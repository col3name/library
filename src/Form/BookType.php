<?php

namespace App\Form;

use App\Entity\Book;
//use App\Form\Type\TagsInputType;
//use App\From\Type\TagType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

/**
 * Class BookType
 * @package App\Form
 */
class BookType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['autofocus' => true],
            ])
            ->add('description', TextareaType::class, [
                'attr' => array('rows' => '5'),
            ])
            ->add('isbn', TextType::class, [
                'attr' => [
                    'minlength' => 11,
                    'maxlength' => 11,
                ],
            ])
//            ->add('imagePath', FileType::class, array('label' => 'Choose JPEG file'))
            ->add('pageNumber', IntegerType::class, [
                'data' => 0,
                'attr' => [
                    'min' => 0,
                    'max' => 10000,
                    'maxLength' => 5
                ]
            ])
            ->add('publicationYear', IntegerType::class, [
                'data' => 0,
                'attr' => [
                    'min' => -3000,
                    'max' => 4000,
                    'maxLength' => 4
                ],
            ])
        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Book::class,
        ));
    }
}