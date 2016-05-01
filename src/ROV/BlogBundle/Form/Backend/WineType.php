<?php
// src/ROV/BlogBundle/Form/Backend/WineType.php
namespace ROV\BlogBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;

use ROV\BlogBundle\Form\Backend\WineryType;

class WineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('brand', TextType::class, array(
                'attr' => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the brand',
                    'required'      => true
                    )
                ))
            ->add('winery', EntityType::class, array(
                'attr' => array('class' => 'form-control'),
                'label'     => 'Select a winery',
                'class'     => 'ROVBlogBundle:Winery',
                'property'  => 'name',
                'empty_value' => ''
                ))
            ->add('type', ChoiceType::class, array(
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
            ->add('varieties', TextType::class, array(
                'attr' => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the varieties',
                    'required'      => false
                    )
                ))
            ->add('year', IntegerType::class, array(
                'attr' => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the year',
                    'maxlength'     => '4',
                    'required'      => false
                    )
                ))
            ->add('points', IntegerType::class, array(
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
            ->add('alcohol', NumberType::class, array(
                'label'     => 'Alcohol by volume',
                'precision' => 1,
                'attr'      => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the alcohol by volume',
                    'min'           => '0',
                    'max'           => '100',
                    'required'      => false
                    )
                ))
            ->add('pvp', NumberType::class, array(
                'label'     => 'Price',
                'precision' => 2,
                'attr'      => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the recommended retail price',
                    'min'           => '0',
                    'max'           => '99999',
                    'required'      => false
                    )
                ))
            ->add('wineMaking', TextareaType::class, array(
                'label' => 'Wine-making',
                'attr'  => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Describe briefly the wine-making process',
                    'required'      => false
                    )
                ))
            ->add('description', TextareaType::class, array(
                'attr' => array(
                    'class'     => 'form-control',
                    'rows'      => 15,
                    'required'  => true
                    )
                ))
            ->add('imageFile', VichFileType::class, array(
                    'required'      => false,
                    'mapping'       => 'wine_image', // mandatory
                    'allow_delete'  => true, // not mandatory, default is true
                    'download_link' => true, // not mandatory, default is true
                ))
            ->add('labelImageFile', VichFileType::class, array(
                    'required'      => false,
                    'mapping'       => 'wine_label', // mandatory
                    'allow_delete'  => true, // not mandatory, default is true
                    'download_link' => true, // not mandatory, default is true
                ))
            ->add('published', CheckboxType::class, array(
                'label'     => 'Publish wine tasting review',
                'attr'      => array('style' => 'margin-left: 5px'),
                'required'  => false
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ROV\BlogBundle\Entity\Wine',
            'validation_groups' => array('Default'),
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'csrf_token_id'       => 'wine_item',
        ));
    }

    public function getBlockPrefix()
    {
        return 'rov_blogbundle_winetype';
    }
}
