<?php
// src/ROV/UsersBundle/Form/Frontend/UserType.php
namespace ROV\UsersBundle\Form\Frontend;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('surname', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('email', 'email', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'options' => array('attr' => array('class' => 'form-control')),
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
                'required' => false
                ))
            ->add('city', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('country', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('address', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('company', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('job', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('allowsEmail', 'checkbox', array(
                'label' => 'Allows email',
                'attr' => array('style' => 'margin-left: 5px'),
                'required' => false
                ))
            ->add('locale', 'choice', array(
                'label' => 'Language',
                'attr' => array('class' => 'form-control'),
                'choices' => array('es' => 'Español', 'en' => 'English'),
                ))
        ;
    }
 
    public function setDefaultOptions(OptionsResolverInterface $resolve)
    {
        $resolve->setDefaults(array(
            'data_class' => 'ROV\UsersBundle\Entity\User',
            'validation_groups' => array('Default', 'signout')
        ));
    }
 
    public function getName()
    {
        return 'rov_usersbundle_usertype';
    }
}