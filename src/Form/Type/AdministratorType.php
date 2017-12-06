<?php
namespace App\Form\Type;

use App\Utils\UserRoles;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\VarDumper\VarDumper;

class AdministratorType extends AbstractType
{
    private $roles;

    public function __construct(ContainerInterface $container)
    {
        $this->roles = $this->flatArray($container->getParameter('security.role_hierarchy.roles'));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
                    'label' => 'name'
                ])
                ->add('username', TextType::class, [
                    'label' => 'login'
                ])
                ->add('email', EmailType::class, [
                    'label' => 'email'
                ])
                ->add('photo', FileType::class, [
                    'attr' => [
                        'accept' => 'image/*'
                    ],
                    'label' => 'photo',
                    'data_class' => null,
                    'required' => false
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'options' => [
                        'label' => 'password'
                    ],
                    'second_options' => [
                        'label' => 'repeat_password'
                    ]
                ])
                ->add('description', TextareaType::class, [
                    'attr' => [
                        'class' => 'tinymce-basic'
                    ],
                    'label' => 'description',
                    'required' => false
                ])
                ->add('facebookProfile', UrlType::class, [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'url'
                    ],
                    'label' => 'facebook'
                ])
                ->add('instagramProfile', TextType::class, [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'username'
                    ],
                    'label' => 'instagram'
                ])
                ->add('twitterProfile', TextType::class, [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'username'
                    ],
                    'label' => 'twitter'
                ])
                ->add('roles', ChoiceType::class, [
                    'choices' => $this->roles,
                    'multiple' => true,
                    'label' => 'role'
                ])
                ->add('submit', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-default'
                    ],
                    'label' => 'save'
                ]);
    }
    private function flatArray(array $data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) === 'ROLE') {
                $result[$key] = constant("App\Utils\UserRoles::$key");
            }
            if (is_array($value)) {
                $temp = $this->flatArray($value);
                if (count($temp) > 0) {
                    $result = array_merge($result, $temp);
                }
            } else {
                $result[$value] = $value;
            }
        }

        asort($result);

        $result = array_flip(array_unique($result));

        return $result;
    }
}
