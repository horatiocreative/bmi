<?php

/*
 * This file is part of the CCDNForum ForumBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrowdValley\Bundle\AppBundle\Form\Forum\Admin\Category;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *
 * @category CCDNForum
 * @package  ForumBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CVNetworkBundle
 *
 */
class CategoryCreateFormType extends AbstractType
{
    /**
     *
     * @access protected
     * @var string $categoryClass
     */
    protected $defaultValue;

    /**
     *
     * @access protected
     * @var string $forumClass
     */
    protected $forumData;

    /**
     *
     * @access protected
     * @var Object $roleHelper
     */
    protected $roleHelper;

    /**
     *
     * @access public
     * @param string $categoryClass
     * @param string $forumClass
     * @param Object $roleHelper
     */
    public function __construct($forumData, $defaultValue)
    {
       $this->forumData = $forumData;
       $this->defaultValue = $defaultValue;
    }

    /**
     *
     * @access public
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('forum', 'choice',
                array(                  
                    'required'           => false,
                    'label'              => 'forum.label',
                    'choices'            => $this->forumData,
                    'data'               => $this->defaultValue,
                    'translation_domain' => 'CVNetworkBundle',
                )
            )
            ->add('name', 'text',
                array(
                    'label'              => 'category.name-label',
                    'translation_domain' => 'CVNetworkBundle',
                )
            )
        ;
    }

    /**
     *
     * @access public
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
//            'data_class'          => $this->categoryClass,
            'csrf_protection'     => true,
            'csrf_field_name'     => '_token',
            // a unique key to help generate the secret token
            'intention'           => 'forum_category_create_item',
            'validation_groups'   => array('forum_category_create'),
            'cascade_validation'  => true,
//            'available_roles'     => $this->roleHelper->getRoleHierarchy(),
            'default_forum'       => null
        ));
    }

    /**
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'Forum_CategoryCreate';
    }
}
