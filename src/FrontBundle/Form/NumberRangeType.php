<?php

namespace FrontBundle\Form;

use AppBundle\Model\NumberRange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumberRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attr = $options['attr'];

        $builder
            ->add('from', HiddenType::class, [
                'required' => false,
                'data' => $attr['min'],
                'empty_data' => $attr['min'],
            ])
            ->add('to', HiddenType::class, [
                'required' => false,
                'data' => $attr['max'],
                'empty_data' => $attr['max'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => NumberRange::class,
        ]);
    }
}
