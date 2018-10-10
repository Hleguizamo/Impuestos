<?php

namespace CertificadosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'text', array(
            'label' => 'Nombre de Usuario:',
            'required' => true,
                'attr' => array(
                    'class' => 'form-control form-control-sm',
                    'placeholder'=>'Nombre del usuario'
                )
            ))
            ->add('username', 'text', array(
            'label' => 'Usuario:',
            'required' => true,
                'attr' => array(
                    'class' => 'form-control form-control-sm',
                    'placeholder'=>'Usuario'
                )
            ))
            ->add('password', 'text', array(
            'label' => 'Clave de acceso:',
            'required' => true,
                'attr' => array(
                    'class' => 'form-control form-control-sm',
                    'placeholder'=>'Clave'
                )
            ))
            ->add('email', 'email', array(
            'label' => 'Email:',
            'required' => true,
                'attr' => array(
                    'class' => 'form-control form-control-sm',
                    'placeholder'=>'email@dominio.com'
                )
            ))
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Impuestos\BackendBundle\Entity\Admin'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'impuestos_backendbundle_admin';
    }
}
