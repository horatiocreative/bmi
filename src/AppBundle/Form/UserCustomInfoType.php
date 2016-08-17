<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Intl\Intl;

class UserCustomInfoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('minInvestmentSize', 'hidden')
                ->add('maxInvestmentSize', 'hidden')
                ->add('region', 'choice', array(
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => array(
                        'Global' => 'Global',
                        'East Asia and Pacific' => 'East Asia and Pacific',
                        'Europe and Central Asia' => 'Europe and Central Asia',                        
                        'Latin America and Caribbean' => 'Latin America and Caribbean',
                        'Middle East and North Africa' => 'Middle East and North Africa',
                        'South Asia' => 'South Asia',
                        'Sub-Saharan Africa' => 'Sub-Saharan Africa',
                    )
                ))
                ->add('sector', 'choice', array(
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => array(
                        'Agriculture' => 'Agriculture',
                        'Education' => 'Education',
                        'Energy and Climate Change' => 'Energy and Climate Change',
                        'Financial Services' => 'Financial Services',
                        'Healthcare' => 'Healthcare',
                        'Infrastructure' => 'Infrastructure',
                        'Real Estate/Housing' => 'Real Estate/Housing',
                        'Other' => 'Other',
                    ),
                    'disabled' => true,
                ))
                ->add('asset_class', 'choice', array(
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => array(
                        'Debt' => 'Debt',
                        'Equity' => 'Equity',
                    )
                ))
                ->add('alt_email', 'text', array('required' => false))
                ->add('investment_size_lower', 'text', array('required' => false))
                ->add('investment_size_upper', 'text', array('required' => false))

            ->getForm();

    }

    public function getName()
    {
        return 'user_custominfo_type';
    }
}
