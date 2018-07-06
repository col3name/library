<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UploadedFileViewTransformer implements DataTransformerInterface
{
    /**
     * @param null $file
     * @return array|mixed
     */
    public function transform($file = null)
    {
        return [
            'file' => $file
        ];
    }

    /**
     *
     * @var $data array
     * @return mixed
     */
    public function reverseTransform($data)
    {
        return $data['file'];
    }
}