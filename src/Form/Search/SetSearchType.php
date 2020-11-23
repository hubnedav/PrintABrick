<?php

namespace App\Form\Search;

use App\Entity\Rebrickable\Set;
use App\Entity\Rebrickable\Theme;
use App\Form\NumberRangeType;
use App\Model\SetSearch;
use App\Repository\Rebrickable\SetRepository;
use App\Repository\Rebrickable\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SetSearchType extends AbstractType
{
    /** @var ThemeRepository */
    private $themeRepository;

    /** @var SetRepository */
    private $setRepository;

    /**
     * SetSearchType constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->themeRepository = $em->getRepository(Theme::class);
        $this->setRepository = $em->getRepository(Set::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', TextType::class, [
                'required' => false,
                'label' => 'set.form.search',
                'attr' => [
                    'placeholder' => 'set.form.search',
                ],
            ])
            ->add('year', NumberRangeType::class, [
                'label' => 'set.form.year',
                'attr' => [
                    'step' => 1,
                    'class' => 'slider',
                    'min' => $this->setRepository->getMinYear(),
                    'max' => $this->setRepository->getMaxYear(),
                ],
            ])
            ->add('partCount', NumberRangeType::class, [
                'label' => 'set.form.partCount',
                'attr' => [
                    'class' => 'slider',
                    'step' => 50,
                    'min' => 1,
                    'max' => $this->setRepository->getMaxPartCount(),
                ],
            ])
            ->add('completeness', NumberRangeType::class, [
                'label' => 'set.form.completeness',
                'attr' => [
                    'class' => 'slider',
                    'step' => 1,
                    'min' => 1,
                    'max' => 100,
                ],
            ])
            ->add('theme', ChoiceType::class, [
                'label' => 'set.form.theme',
                'choices' => $this->themeRepository->findAll(),
                'choice_label' => 'fullName',
                'choice_translation_domain' => false,
                'group_by' => function ($theme, $key, $index) {
                    return $theme->getGroup()->getName();
                },
                'choice_value' => 'id',
                'placeholder' => 'set.form.theme.all',
                'required' => false,
                'attr' => [
                    'placeholder' => 'set.form.theme.placeholder',
                    'class' => 'select2 dropdown ui',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => SetSearch::class,
            'method' => 'GET',
        ]);
    }
}
