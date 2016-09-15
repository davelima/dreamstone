<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class AdministratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
                ->add('username')
                ->add('email')
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'options' => [
                        'label' => 'Password'
                    ],
                    'second_options' => [
                        'label' => 'Repeat password'
                    ]
                ])
                ->add('super_user', CheckboxType::class, [
                    'required' => false
                ])
                ->add('submit', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-default'
                    ],
                    'label' => 'Save'
                ]);
    }
}
