<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class CompleteRegistrationInvestorType extends AbstractType
{
    private $user;

    public function __construct(array $user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('given_name', 'text', array(
            'required' => true, 'data'=>isset($this->user['given_name'])?$this->user['given_name']:''
        ))->add('family_name', 'text', array(
            'required' => true,
            'data'=>isset($this->user['family_name'])?$this->user['family_name']:''
        ))->add('phone_1', 'text', array(
            'required' => true,
        ))->add('birth_date', 'hidden')
        ->add('address', new CompleteRegistrationAddressType())
        ;
    }

    public function getName()
    {
        return 'complete_registration_investor_type';
    }
}
