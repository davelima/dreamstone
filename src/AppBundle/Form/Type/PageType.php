<?php
namespace AppBundle\Form\Type;

use AppBundle\Entity\Page;
use AppBundle\Entity\Section;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PageType extends AbstractType
{
    /**
     * @var Page
     */
    private $entity;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->entity = $options['data'];

        $builder->add('title', TextType::class, [
                    'label' => 'Título'
                ])
                ->add('slug', TextType::class, [
                    'label' => 'URL',
                    'required' => false
                ])
                ->add('content', TextareaType::class, [
                    'attr' => [
                        'class' => 'tinymce'
                    ],
                    'label' => 'Texto'
                ])
                ->add('image', FileType::class, [
                    'attr' => [
                        'accept' => 'image/*'
                    ],
                    'required' => false,
                    'data_class' => null,
                    'label' => 'Imagem'
                ])
                ->add('showOnMenu', CheckboxType::class, [
                    'label' => 'Exibir no menu?',
                    'required' => false
                ])
                ->add('submit', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-default'
                    ],
                    'label' => 'Salvar'
                ]);
    }
}
