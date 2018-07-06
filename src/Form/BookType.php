<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
//use App\Form\Type\TagsInputType;
//use App\From\Type\TagType;
use App\Entity\Genre;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                    'maxlength' => 20,
                ],
            ]);

        $this->add($builder, 'tags', Tag::class, 'Теги');
        $this->add($builder, 'genresBook', Genre::class, 'Жанры книги');
        $this->add($builder, 'authorsBook', Author::class, 'Авторы книги');

        $builder->add('pageNumber', IntegerType::class, [
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

    private function add(FormBuilderInterface $formBuilder, string $child, string $entityName, string $title){
        $formBuilder->add($child, EntityType::class, [
            'class' => $entityName,
            'choice_label' => 'name',
            'placeholder' => $title,
            'multiple' => true,
            'expanded' => true,
            'attr' => [
                'data-dropdown-item' => $child,
            ]
        ]);
    }
}