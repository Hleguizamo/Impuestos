<?php

namespace AdministradorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class AdminType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array(
            'label' => 'Nombre de Usuario:',
            'required' => true,
                'attr' => array(
                    'class' => 'form-control form-control-sm',
                    'placeholder'=>'Nombre del usuario'
                )
            ))
            ->add('username', TextType::class, array(
            'label' => 'Usuario:',
            'required' => true,
                'attr' => array(
                    'class' => 'form-control form-control-sm',
                    'placeholder'=>'Usuario'
                )
            ))
            ->add('password', TextType::class, array(
            'label' => 'Clave de acceso:',
            'required' => true,
                'attr' => array(
                    'class' => 'form-control form-control-sm',
                    'placeholder'=>'Clave'
                )
            ))
            ->add('email', EmailType::class, array(
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
            'data_class' => 'Impuestos\AdministradorBundle\Entity\Admin'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'impuestos_administradorbundle_admin';
    }
}
