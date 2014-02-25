<?php
// src/ROV/UsersBundle/Form/Frontend/UserSettingsType.php
namespace ROV\UsersBundle\Form\Frontend;

use ROV\UsersBundle\Form\Frontend\UserType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Edit user profile form
 */
class UserSettingsType extends UserType {

	/**
	 * Edit user profile uses a different validation 
	 */
    public function setDefaultOptions(OptionsResolverInterface $resolve)
    {
        $resolve->setDefaults(array(
            'data_class' => 'ROV\UsersBundle\Entity\User',
            'validation_groups' => array('Default')
        ));
    }
}