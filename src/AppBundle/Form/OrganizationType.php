<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Intl\Intl;

class OrganizationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('display_name', 'text', array('required'=> true))
            ->add('brief_desc', 'text', array('required'=> true))
            ->add('sector', 'text', array('required'=> true))
            ->add('location', 'text', array('required'=> true))
            ->add('detail_desc', 'textarea', array('required'=> true))
            ->add('org_email', 'text', array('required'=> false))
            ->add('telephone', 'text', array('required'=> false))
            ->add('website', 'text', array('required'=> false))
            ->add('facebook', 'text', array('required'=> false))
            ->add('twitter', 'text', array('required'=> false))
            ->add('linkedin', 'text', array('required'=> false))

            ->getForm();

    }

    public function getName()
    {
        return 'organization_type';
    }
}
