<?php
// src/ROV/BlogBundle/Form/Backend/ArticleType.php
namespace ROV\BlogBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use ROV\BlogBundle\Form\Backend\CategoryType;
use ROV\BlogBundle\Form\Backend\TagType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'attr' => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the title',
                    'required'      => true
                    )
                ))
            ->add('subtitle', TextType::class, array(
                'attr' => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the subtitle or summary',
                    'required'      => false
                    )
                ))
            ->add('image', UrlType::class, array(
                'attr' => array(
                    'class'         => 'form-control',
                    'placeholder'   => 'Type the image url',
                    'required'      => false
                    )
                ))
            ->add('content', TextareaType::class, array(
                'attr' => array(
                    'class'     => 'form-control',
                    'rows'      => 20,
                    'required'  => true
                    )
                ))
            ->add('published', CheckboxType::class, array(
                'label'     => 'Publish article',
                'attr'      => array('style' => 'margin-left: 5px'),
                'required'  => false
                ))
            ->add('category', EntityType::class, array(
                'attr' => array('class' => 'form-control'),
                'label'     => 'Choose a category',
                'class'     => 'ROVBlogBundle:Category',
                'property'  => 'name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'empty_value' => ''
                ))
            ->add('tags', EntityType::class, array(
                'attr'      => array('class' => 'form-control'),
                // 'type'         => new TagType(),
                'class'     => 'ROVBlogBundle:Tag',
                'property'  => 'name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'multiple'  => true,
                'empty_value' => ''
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ROV\BlogBundle\Entity\Article',
            'validation_groups' => array('Default'),
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'csrf_token_id'       => 'article_item',
        ));
    }

    public function getBlockPrefix()
    {
        return 'rov_blogbundle_articletype';
    }
}
