<?php

namespace FrontBundle\Form\Search;

use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Model\SetSearch;
use AppBundle\Repository\Rebrickable\SetRepository;
use AppBundle\Repository\Rebrickable\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use FrontBundle\Form\NumberRangeType;
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
     *
     * @param EntityManagerInterface $em
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
                    'min' => 0,
                    'max' => $this->setRepository->getMaxPartCount(),
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
