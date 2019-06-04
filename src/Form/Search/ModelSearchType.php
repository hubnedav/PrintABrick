<?php

namespace App\Form\Search;

use App\Entity\LDraw\Category;
use App\Model\ModelSearch;
use App\Repository\LDraw\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModelSearchType extends AbstractType
{
    /** @var CategoryRepository */
    private $categoryRepository;

    /**
     * ModelSearchType constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->categoryRepository = $em->getRepository(Category::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', TextType::class, [
                'required' => false,
                'label' => 'brick.form.search',
                'attr' => [
                    'placeholder' => 'brick.form.search',
                ],
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'brick.form.category',
                'choices' => $this->categoryRepository->findAll(),
                'placeholder' => 'brick.form.category.all',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'choice_value' => 'id',
                'required' => false,
                'attr' => [
                    'class' => 'ui dropdown search selection'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => ModelSearch::class,
            'method' => 'GET',
        ]);
    }
}
