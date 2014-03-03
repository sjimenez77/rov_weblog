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
                    'class' => 'form-control',
                    'placeholder' => 'Type the title',
                    'required' => true
                    )
                ))
            ->add('subtitle', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the subtitle or summary',
                    'required' => false
                    )
                ))
            ->add('image', 'url', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the image url',
                    'required' => false
                    )
                ))
            ->add('content', 'textarea', array(
                'attr' => array(
                    'class' => 'form-control',
                    'rows' => 10,
                    'required' => true
                    )
                ))
            ->add('published', 'checkbox', array(
                'label' => 'Publish article',
                'attr' => array('style' => 'margin-left: 5px'),
                'required' => false
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