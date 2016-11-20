<?php

namespace AppBundle\Client\Rebrickable\Converter;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PartPropertyNameConverter implements NameConverterInterface
{
    public function normalize($propertyName)
    {
        return $propertyName;
    }

    public function denormalize($propertyName)
    {
        switch ($propertyName) {
            case 'part_name': return 'name';
            case 'part_id': return 'id';
            case 'part_type_id': return 'typeId';
            case 'rb_color_id': return 'colorId';
            default: return $propertyName;
        }
    }
}
