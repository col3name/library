<?php

namespace App\Form;

use App\Entity\BookCopy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BookCopyType
 * @package App\Form
 */
class BookCopyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('count', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => BookCopy::MAX_COUNT
                ]
            ])
            ->add('book', BookType::class)
            ->add('imagePath', FileType::class, array('data_class' => null))
            ->add('filePath', FileType::class, array('data_class' => null))
        ;

//        $builder
//            ->add('name', TextType::class, [
//                'attr' => ['autofocus' => true],
//            ])
//            ->add('description', TextareaType::class, [
//                'attr' => array('rows' => '5'),
//            ])
//            ->add('isbn', TextType::class, [
//                'attr' => [
//                    'minlength' => 11,
//                    'maxlength' => 11,
//                ],
//            ])
////            ->add('imagePath', FileType::class, array('label' => 'Choose JPEG file'))

//            ->add('publicationYear', IntegerType::class, [
//                'data' => 0,
//                'attr' => [
//                    'min' => -3000,
//                    'max' => 4000,
//                    'maxLength' => 4
//                ],
//            ])
//            ->add('genres', GenreType::class)
//            ->add('tags', TagsInputType::class, [
//                'label' => 'label.tags',
//                'required' => false,
//            ])
        ;
//            ->add('genres', GenreType::class);

//        $builder->get('imagePath')->addModelTransformer(new CallBackTransformer(
//        /**
//         * @param $imageUrl
//         * @return mixed
//         */
//            function($imageUrl) {
//                return null;
//            },
//            function($imageUrl) {
//                return $imageUrl;
//            }
//        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => BookCopy::class,
        ));
    }
}