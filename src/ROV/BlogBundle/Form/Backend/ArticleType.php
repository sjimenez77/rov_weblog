<?php
// src/ROV/BlogBundle/Form/Backend/ArticleType.php
namespace ROV\BlogBundle\Form\Backend;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'attr' => array(
                    'class' => 'form-control input-lg',
                    'placeholder' => 'Type in your name',
                    'required' => true
                    )
                ))
            ->add('subtitle', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type in your surname',
                    'required' => false
                    )
                ))
            ->add('content', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'required' => true
                    )
                ))
        ;
    }
 
    public function setDefaultOptions(OptionsResolverInterface $resolve)
    {
        $resolve->setDefaults(array(
            'data_class' => 'ROV\BlogBundle\Entity\Article',
            'validation_groups' => array('Default')
        ));
    }
 
    public function getName()
    {
        return 'rov_blogbundle_articletype';
    }
}