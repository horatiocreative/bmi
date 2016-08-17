<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Intl\Intl;

class UserType extends AbstractType
{
    private function getAllCountries()
    {
        \Locale::setDefault('en');
        $countries = Intl::getRegionBundle()->getCountryNames();
        $arr = [];
        if (is_array($countries)) {
            $arr = array_combine($countries, $countries);
        } else {
            $arr = ['United States' => 'United States', 'United Kingdom' => 'United Kingdom']; //return common countries
        }
        return $arr;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('given_name', 'text', array('required'=> true))
            ->add('family_name', 'text', array('required'=> true))
            ->add('biography', 'textarea', array('required'=> false))
            ->add('phone_1', 'text', array('required'=> false))
            ->add('phone_2', 'text', array('required'=> false))
            ->add('birth_date', 'text', array('required'=> false))
            ->add('tax_id', 'text', array('required'=> false))
            ->add('nationality', 'choice', array(
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'choices' => $this->getAllCountries(),
                'empty_data'  => null,
                'placeholder' => '-- Select your country --',
            ))
            ->add('address', new AddressType())
            ->add('info', new UserCustomInfoType())
            ->add('organization_name', 'text', array('required'=> false))
            ->add('position', 'text', array('required'=> false))
            ->getForm();

    }

    public function getName()
    {
        return 'user_type';
    }
}
