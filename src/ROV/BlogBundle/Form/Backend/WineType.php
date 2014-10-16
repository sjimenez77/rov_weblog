<?php
// src/ROV/BlogBundle/Form/Backend/WineType.php
namespace ROV\BlogBundle\Form\Backend;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use ROV\BlogBundle\Form\Backend\WineryType;
 
class WineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('brand', 'text', array(
                'attr' => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the brand',
                    'required'      => true
                    )
                ))
            ->add('winery', 'entity', array(
                'attr' => array('class' => 'form-control'),
                'label'     => 'Select a winery',
                'class'     => 'ROVBlogBundle:Winery',
                'property'  => 'name',
                'empty_value' => ''
                ))
            ->add('type', 'choice', array(
                'attr' => array('class' => 'form-control'),
                'empty_value' => '',
                'choices' => array(
                    'B'     => 'white',
                    'BC'    => 'vintage white',
                    'BFB'   => 'matured white in barrel',
                    'RD'    => 'rosé',
                    'T'     => 'red',
                    'TC'    => 'vintage red',
                    'TR'    => 'mature red',
                    'TGR'   => 'gran reserva red',
                    'FI'    => 'fino',
                    'MZ'    => 'manzanilla',
                    'OL'    => 'oloroso',
                    'OLV'   => 'old oloroso',
                    'AM'    => 'amontillado',
                    'PX'    => 'pedro ximénez',
                    'PC'    => 'palo cortado',
                    'CR'    => 'cream',
                    'PCR'   => 'pale cream',
                    'GE'    => 'generoso',
                    'ESP'   => 'sparkling',
                    'BR'    => 'brut',
                    'BN'    => 'brut nature',
                    'SC'    => 'dry',
                    'SS'    => 'semi-dry',
                    'S/C'   => 'no vintage',
                    )
                ))
            ->add('varieties', 'text', array(
                'attr' => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the varieties',
                    'required'      => false
                    )
                ))
            ->add('year', 'integer', array(
                'attr' => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the year',
                    'maxlength'     => '4',
                    'required'      => false
                    )
                ))
            ->add('points', 'integer', array(
                'attr' => array(
                    'class'         => 'form-control',
                    'style'         => 'font-size: 3em; height: auto',
                    'placeholder'   => '0 - 100',
                    'min'           => '0',
                    'max'           => '100',
                    'maxlength'     => '3',
                    'size'          => '3',
                    'required'      => false
                    )
                ))
            ->add('description', 'textarea', array(
                'attr' => array(
                    'class'     => 'form-control',
                    'rows'      => 20,
                    'required'  => true
                    )
                ))
            ->add('imageFile', 'vich_file', array(
                    'required'      => false,
                    'mapping'       => 'wine_image', // mandatory
                    'allow_delete'  => true, // not mandatory, default is true
                    'download_link' => true, // not mandatory, default is true
                ))
            ->add('published', 'checkbox', array(
                'label'     => 'Publish wine tasting review',
                'attr'      => array('style' => 'margin-left: 5px'),
                'required'  => false
                ))
        ;
    }
 
    public function setDefaultOptions(OptionsResolverInterface $resolve)
    {
        $resolve->setDefaults(array(
            'data_class' => 'ROV\BlogBundle\Entity\Wine',
            'validation_groups' => array('Default'),
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'wine_item',
        ));
    }
 
    public function getName()
    {
        return 'rov_blogbundle_winetype';
    }
}