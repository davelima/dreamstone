<?php
namespace App\Form\Type;

use App\Entity\Section;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SectionType extends AbstractType
{
    /**
     * @var Section
     */
    private $entity;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->entity = $options['data'];

        $builder->add('title', TextType::class, [
                    'label' => 'title'
                ])
                ->add('showOnMenu', CheckboxType::class, [
                    'label' => 'show_on_menu',
                    'required' => false
                ])
                ->add('parentSection', EntityType::class, [
                    'class' => 'App:Section',
                    'query_builder' => function (EntityRepository $er) {
                        if ($this->entity->getId()) {
                            return $er->createQueryBuilder('u')
                                ->where("u.id != '{$this->entity->getId()}'");
                        } else {
                            return $er->createQueryBuilder('u');
                        }
                    },
                    'choice_label' => 'title',
                    'label' => 'parent_section',
                    'placeholder' => 'choose',
                    'required' => false
                ])
                ->add('submit', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-default'
                    ],
                    'label' => 'save'
                ]);
    }
}
