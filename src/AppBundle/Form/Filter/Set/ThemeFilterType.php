<?php

namespace AppBundle\Form\Filter\Set;

use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Repository\Rebrickable\ThemeRepository;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemeFilterType extends AbstractType
{
    private $themeRepository;

    public function __construct(ThemeRepository $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', Filters\ChoiceFilterType::class, [
            'choices' => $this->themeRepository->findAllMain(),
            'choice_label' => function ($theme, $currentChoiceKey) {
                if ($parent = $theme->getParent()) {
                    if ($parentParent = $parent->getParent()) {
                        if ($parentParentParent = $parentParent->getParent()) {
                            return $parentParentParent->getName().' > '.$parentParent->getName().' > '.$parent->getName().' > '.$theme->getName();
                        }

                        return $parentParent->getName().' > '.$parent->getName().' > '.$theme->getName();
                    }

                    return $parent->getName().' > '.$theme->getName();
                }

                return $theme->getName();
            },
            'label' => 'filter.set.theme',
//            'attr' => [
//                'class' => 'ui dropdown search selection'
//            ]
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
