<?php

namespace AppBundle\Api\Client\Rebrickable\Converter;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PropertyNameConverter implements NameConverterInterface
{
    public function normalize($propertyName)
    {
        return $propertyName;
    }

    public function denormalize($propertyName)
    {
        switch ($propertyName) {
            case 'part_num': return 'number';
            case 'part_cat_id': return 'categoryId';
            case 'part_img_url': return 'imgUrl';
            case 'part_url': return 'url';
            case 'set_num': return 'number';
            case 'set_img_url': return 'imgUrl';
            case 'set_url': return 'url';
            default: return $propertyName;
        }
    }
}
