<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Intl\Intl;

class OfferingType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('required'=> true))
            ->add('funding_goal', 'text', array('required'=> true))
            ->add('external_commitments', 'text', array('required'=> true))
            ->add('valuation', 'text', array('required'=> true))
            ->add('equity_offered', 'textarea', array('required'=> true))
            ->add('offering_description', 'text', array('required'=> false))

            ->getForm();

    }

    public function getName()
    {
        return 'offering_type';
    }
}
