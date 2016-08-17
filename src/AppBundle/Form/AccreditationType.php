<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class AccreditationType extends AbstractType
{
    private $user;

    public function __construct(array $user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('additional_type', 'hidden')
            ->add('info', new AccreditationCustomInfoType());
    }

    public function getName()
    {
        return 'accreditation_type';
    }
}
