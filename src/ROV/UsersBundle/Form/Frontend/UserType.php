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
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type in your name',
                    'required' => true
                    )
                ))
            ->add('surname', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type in your surname',
                    'required' => true
                    )
                ))
            ->add('email', 'email', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type in your email',
                    'required' => true
                    )
                ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_options'  => array(
                    'label' => 'Password',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Type in your password'
                        )
                    ),
                'second_options' => array(
                    'label' => 'Repeat Password',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Type in your password once more'
                        )
                    ),
                'required' => false
                ))
            ->add('city', 'text', array(
                'attr' => array('class' => 'form-control')
                ))
            ->add('country', 'country', array(
                'attr' => array('class' => 'form-control'),
                'empty_value' => ''
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
            'validation_groups' => array('Default', 'signup'),
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'user_item',
            'translation_domain' => 'messages'
        ));
    }
 
    public function getName()
    {
        return 'rov_usersbundle_usertype';
    }
}