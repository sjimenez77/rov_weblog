<?php
// src/ROV/BlogBundle/Form/Backend/WineryType.php
namespace ROV\BlogBundle\Form\Backend;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
use ROV\BlogBundle\Form\Backend\RegionType;

class WineryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery name',
                    'required' => true
                    )
                ))
            ->add('region', 'entity', array(
                'attr' => array('class' => 'form-control'),
                'label'     => 'Choose a region',
                'class'     => 'ROVBlogBundle:Region',
                'property'  => 'name',
                'empty_value' => ''
                ))
            ->add('address', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery address',
                    'required' => false
                    )
                ))
            ->add('location', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery location, city, town...',
                    'required' => false
                    )
                ))
            ->add('postal', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery postal code',
                    'required' => false
                    )
                ))
            ->add('phone', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery phone',
                    'required' => false
                    )
                ))
            ->add('email', 'email', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery email',
                    'required' => false
                    )
                ))
            ->add('web', 'url', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery website URL',
                    'required' => false
                    )
                ))
        ;
    }
 
    public function setDefaultOptions(OptionsResolverInterface $resolve)
    {
        $resolve->setDefaults(array(
            'data_class' => 'ROV\BlogBundle\Entity\Winery',
            'validation_groups' => array('Default'),
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'winery_item',
        ));
    }
 
    public function getName()
    {
        return 'rov_blogbundle_winerytype';
    }
}