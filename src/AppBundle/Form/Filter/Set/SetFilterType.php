<?php

namespace AppBundle\Form\Filter\Set;

use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SetFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('search', Filters\TextFilterType::class, [
            'apply_filter' => [$this, 'setSearchCallback'],
            'label' => 'filter.part.search',
        ]);

        $builder->add('partCount', Filters\NumberRangeFilterType::class, [
            'label' => 'filter.part.partCount',
        ]);

        $builder->add('year', Filters\NumberRangeFilterType::class, [
            'label' => 'filter.part.year',
        ]);

        $builder->add('theme', ThemeFilterType::class, [
            'add_shared' => function (FilterBuilderExecuterInterface $builderExecuter) {
                $builderExecuter->addOnce($builderExecuter->getAlias().'.theme', 'c', function (QueryBuilder $filterBuilder, $alias, $joinAlias, $expr) {
                    $filterBuilder->leftJoin($alias.'.theme', $joinAlias);
                });
            },
        ]);

    }

    public function getBlockPrefix()
    {
        return 'model_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'validation_groups' => ['filtering'], // avoid NotBlank() constraint-related message
        ]);
    }

    public function setSearchCallback(QueryInterface $filterQuery, $field, $values)
    {
        if (empty($values['value'])) {
            return null;
        }

        // expression that represent the condition
        $expression = $filterQuery->getExpr()->orX(
            $filterQuery->getExpr()->like('s.name', ':value'),
            $filterQuery->getExpr()->like('s.number', ':value')
        );

        return $filterQuery->createCondition($expression, ['value' => '%'.$values['value'].'%']);
    }
}
