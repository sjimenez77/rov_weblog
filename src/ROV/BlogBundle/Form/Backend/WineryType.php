<?php
// src/ROV/BlogBundle/Form/Backend/WineryType.php
namespace ROV\BlogBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use ROV\BlogBundle\Form\Backend\RegionType;

class WineryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery name',
                    'required' => true
                    )
                ))
            ->add('region', EntityType::class, array(
                'attr' => array('class' => 'form-control'),
                'label'     => 'Choose a region',
                'class'     => 'ROVBlogBundle:Region',
                'property'  => 'name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.name', 'ASC');
                },
                'empty_value' => ''
                ))
            ->add('address', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery address',
                    'required' => false
                    )
                ))
            ->add('location', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery location, city, town...',
                    'required' => false
                    )
                ))
            ->add('postal', TextType::class, array(
                'label' => 'Postal code',
                'attr'  => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery postal code',
                    'required' => false
                    )
                ))
            ->add('phone', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery phone',
                    'required' => false
                    )
                ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery email',
                    'required' => false
                    )
                ))
            ->add('web', UrlType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Type the winery website URL',
                    'required' => false
                    )
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ROV\BlogBundle\Entity\Winery',
            'validation_groups' => array('Default'),
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'csrf_token_id'       => 'winery_item',
        ));
    }

    public function getBlockPrefix()
    {
        return 'rov_blogbundle_winerytype';
    }
}
