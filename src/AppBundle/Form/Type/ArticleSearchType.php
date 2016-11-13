<?php

namespace AppBundle\Form\Type;

use AppBundle\Model\ArticleSearch;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class ArticleSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',null,array(
                'required' => false,
            ))
            ->add('dateFrom', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
            ))
            ->add('dateTo', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
            ))
            ->add('isPublished', ChoiceType::class, array(
                'choices' => array('false'=>'non','true'=>'oui'),
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            // avoid to pass the csrf token in the url (but it's not protected anymore)
            'csrf_protection' => false,
            'data_class' => 'AppBundle\Model\ArticleSearch'
        ));
    }

    public function getName()
    {
        return 'article_search_type';
    }
}