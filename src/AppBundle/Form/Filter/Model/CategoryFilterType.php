<?php

namespace AppBundle\Form\Filter\Model;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Repository\LDraw\CategoryRepository;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFilterType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', Filters\ChoiceFilterType::class, [
            'choices' => $this->categoryRepository->findAll(),
            'choice_label' => 'name',
            'label' => 'filter.part.category',
        ]);
    }

    public function getParent()
    {
        return Filters\SharedableFilterType::class; // this allow us to use the "add_shared" option
    }

    public function getBlockPrefix()
    {
        return 'category_filter';
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'csrf_protection' => false,
            'validation_groups' => ['filtering'], // avoid NotBlank() constraint-related message
            'method' => 'GET',
        ]);
    }
}
