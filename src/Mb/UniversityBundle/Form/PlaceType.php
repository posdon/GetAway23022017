<?php

namespace Mb\UniversityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlaceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('places')
            ->add('university', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
              'class'    => 'MbUniversityBundle:University',
              'property' => 'name',
              'multiple' => false
            ))
            ->add('mobilityPeriod', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
              'class'    => 'MbUniversityBundle:MobilityPeriod',
              'property' => 'type',
              'multiple' => false
            ))
            ->add('department', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
              'class'    => 'MbMainBundle:Department',
              'property' => 'name',
              'multiple' => false
            ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mb\UniversityBundle\Entity\Place'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mb_universitybundle_place';
    }
}
