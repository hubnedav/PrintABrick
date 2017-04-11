<?php

namespace AppBundle\Form\Filter\Set;

use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Entity\LDraw\Category;
use AppBundle\Manager\LDraw\CategoryManager;
use AppBundle\Manager\RebrickableManager;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemeFilterType extends AbstractType
{
    private $rebrickableManager;

    public function __construct(RebrickableManager $rebrickableManager)
    {
        $this->rebrickableManager = $rebrickableManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', Filters\ChoiceFilterType::class, [
            'choices' => $this->rebrickableManager->FindAllThemes(),
            'choice_label' =>  function ($allChoices, $currentChoiceKey) {

                dump($currentChoiceKey);

                $parent = $allChoices->getParent();

                return $parent ? $parent->getName().' > '.$allChoices->getName() : $allChoices->getName();
            },
            'label' => 'filter.set.theme',
        ]);
    }

    public function getParent()
    {
        return Filters\SharedableFilterType::class; // this allow us to use the "add_shared" option
    }

    public function getBlockPrefix()
    {
        return 'theme_filter';
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Theme::class,
            'csrf_protection' => false,
            'validation_groups' => ['filtering'], // avoid NotBlank() constraint-related message
            'method' => 'GET',
        ]);
    }
}
