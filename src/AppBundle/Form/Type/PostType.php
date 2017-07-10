<?php
namespace AppBundle\Form\Type;

use AppBundle\Entity\Administrator;
use AppBundle\Entity\Post;
use AppBundle\Entity\Section;
use AppBundle\Utils\UserRoles;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\VarDumper\VarDumper;

class PostType extends AbstractType
{
    /**
     * @var Section
     */
    private $entity;

    /**
     * @var AuthorizationChecker
     */
    private $auth;

    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->auth = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->entity = $options['data'];

        $builder->add('title', TextType::class, [
                'label' => 'title'
            ])
            ->add('section', EntityType::class, [
                'class' => 'AppBundle:Section',
                'choice_label' => 'title',
                'required' => true,
                'placeholder' => 'choose',
                'label' => 'section'
            ])
            ->add('author', EntityType::class, [
                'class' => 'AppBundle:Administrator',
                'choice_label' => 'name',
                'required' => true,
                'placeholder' => 'author',
                'label' => 'author',
                'query_builder' => function(EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('u');
                    return $qb->where($qb->expr()->eq('u.isActive', '?1' ))
                        ->setParameter('1', true);
                },
            ])
            ->add('status', ChoiceType::class, [
                'choices' => $this->getStatusOptions(),
                'label' => 'status'
            ])
            ->add('tags', TextType::class, [
                'required' => false,
                'label' => 'tags'
            ])
            ->add('pubDate', TextType::class, [
                'attr' => [
                    'type' => 'datetime'
                ],
                'label' => 'pub_date'
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'tinymce-basic'
                ],
                'label' => 'short_description'
            ])
            ->add('body', TextareaType::class, [
                'attr' => [
                    'class' => 'tinymce',
                    'style' => 'height: 300px'
                ],
                'label' => 'text'
            ])

            ->add('image', FileType::class, [
                'attr' => [
                    'accept' => 'image/*'
                ],
                'required' => false,
                'data_class' => null,
                'label' => 'image'
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-default'
                ],
                'label' => 'save'
            ]);
    }

    private function getStatusOptions()
    {
        $options = Post::$statusLabels;
        $permissions = Post::$statusPermissions;

        foreach ($options as $index => $value) {
            if (! $this->auth->isGranted($permissions[$index])) {
                unset($options[$index]);
            }
        }

        return array_flip($options);
    }
}
