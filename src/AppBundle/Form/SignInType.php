<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;

class SignInType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'constraints' => new NotBlank(),
                'required' => false,
            ))
            ->add('password', 'password', array(
                'constraints' => new NotBlank(),
                'required' => false,
            ))
            ->add('remember', 'checkbox', array(
                'required' => false
            ))
            ->getForm();
    }

    public function getName()
    {
        return 'sign_in_type';
    }
}
