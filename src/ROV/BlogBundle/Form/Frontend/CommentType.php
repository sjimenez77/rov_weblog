<?php
// src/ROV/BlogBundle/Form/Frontend/CommentType.php
namespace ROV\BlogBundle\Form\Frontend;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array(
                'attr' => array(
                    'class' => 'form-control',
                    'rows' => 6,
                    'required' => true
                    )
                ))
        ;
    }
 
    public function setDefaultOptions(OptionsResolverInterface $resolve)
    {
        $resolve->setDefaults(array(
            'data_class' => 'ROV\BlogBundle\Entity\Comment',
            'validation_groups' => array('Default'),
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'comment_item',
        ));
    }
 
    public function getName()
    {
        return 'rov_blogbundle_commenttype';
    }
}