<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;

class SignUpType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'constraints' => new NotBlank(),
                'required' => false,
            ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match',
                'required' => false,
            ))
            ->add('confirm_terms', 'checkbox', array(
                'constraints' => new NotBlank(),
                'required' => false,
            ))
            ->add('given_name', 'text', array(
                'constraints' => new NotBlank(),
                'required' => false,
            ))
            ->add('family_name', 'text', array(
                'constraints' => new NotBlank(),
                'required' => false,
            ))
            ->add('organization_name', 'text', array(
                'required' => false,
            ))
            ->add('position', 'text', array(
                'required' => false,
            ))
            ->getForm();
    }

    public function getName()
    {
        return 'sign_up_type';
    }

    public function getDefaultOptions(array $options)
    {
        return array('csrf_protection' => false);
    }

}
