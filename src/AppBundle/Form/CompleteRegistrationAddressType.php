<?php


namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CompleteRegistrationAddressType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('building', 'text', array(
            'required' => true,
        ))->add('street_name', 'text', array(
            'required' => true,
        ))->add('region', 'text', array(
            'required' => true,
        ))->add('city', 'text', array(
            'required' => true,
        ))->add('postal_code', 'text', array(
            'required' => true,
        ))
            ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'address_form_type';
    }
}