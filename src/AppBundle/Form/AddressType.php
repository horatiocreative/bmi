<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('building', 'text', array('required' => false))
            ->add('street_address', 'text', array('required' => false))
            ->add('postal_code', 'text', array('required' => false))
            ->add('city', 'text', array('required' => false))
            ->add('region', 'text', array('required' => false))
        ;
    }

    public function getName()
    {
    }
}
