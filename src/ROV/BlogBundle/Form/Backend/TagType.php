<?php
// src/ROV/BlogBundle/Form/Backend/TagType.php
namespace ROV\BlogBundle\Form\Backend;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the tag name',
                    'required' => true
                    )
                ))
        ;
    }
 
    public function setDefaultOptions(OptionsResolverInterface $resolve)
    {
        $resolve->setDefaults(array(
            'data_class' => 'ROV\BlogBundle\Entity\Tag',
            'validation_groups' => array('Default')
        ));
    }
 
    public function getName()
    {
        return 'rov_blogbundle_tagtype';
    }
}