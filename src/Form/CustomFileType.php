<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class CommentType
 * @package App\Form
 */
class CustomFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('avatar', FileType::class, [
            'data_class' => null,
            'attr' => [
                'data-upload-image' => 'avatar'
            ]
        ])
            ->add('submit', SubmitType::class, [
                'label' => 'сохранить',
                'attr' => [
                    'class' => 'button fi-upload fi-left'
                ],
            ])
        ;
//        $builder->addViewTransformer(new UploadedFileViewTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}