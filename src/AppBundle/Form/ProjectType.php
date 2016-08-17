<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('display_name', 'text', array(
                    'required' => false,
                ))
                ->add('brief_desc', 'textarea', array(
                    'attr' => array('rows' => '5'),
                    'required' => false,
                    ))
                ->add('location', 'text', array(
                    'required' => false
                ))
                ->add('offering_info', new ProjectOfferingType())
                ->add('file', new ProjectFileType(),array('label'=>false))
                ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project_type';
    }
}
