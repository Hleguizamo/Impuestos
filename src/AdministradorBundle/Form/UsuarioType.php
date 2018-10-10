<?php

namespace AdministradorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UsuarioType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nit', TextType::class, array(
                'label' => 'Nit del Proveedor:',
                'required' => true,
                    'attr' => array(
                        'class' => 'form-control form-control-sm',
                        'placeholder'=>'NIT'
                    )
                ))
            ->add('nombre', TextType::class, array(
                'label' => 'Razon Social:',
                'required' => true,
                    'attr' => array(
                        'class' => 'form-control form-control-sm',
                        'placeholder'=>'Razon social del proveedor'
                    )
                ))
            ->add('direccion', TextType::class, array(
                'label' => 'Dirección:',
                'required' => true,
                    'attr' => array(
                        'class' => 'form-control form-control-sm',
                        'placeholder'=>'Calle 1 # 1 -1'
                    )
                ))
             ->add('tel', TextType::class, array(
                'label' => 'Teléfono:',
                'required' => true,
                    'attr' => array(
                        'class' => 'form-control form-control-sm',
                        'placeholder'=>'437 51 50'
                    )
                ))
            ->add('representante', TextType::class, array(
                'label' => 'Representante Legal:',
                'required' => true,
                    'attr' => array(
                        'class' => 'form-control form-control-sm',
                        'placeholder'=>'Nombre del Representante'
                    )
                ))
            ->add('mail', EmailType::class, array(
                'label' => 'Correo electronico:',
                'required' => true,
                    'attr' => array(
                        'class' => 'form-control form-control-sm',
                        'placeholder'=>'Email'
                    )
                ))
            ->add('password', TextType::class, array(
                'label' => 'Clave de acceso:',
                'required' => true,
                    'attr' => array(
                        'class' => 'form-control form-control-sm',
                        'placeholder'=>'Password'
                    )
                ))
            ->add('enabled', ChoiceType::class, array(
                'choices' => array(
                    'Activado' => '1',
                    'Desactivado' => '0'
                ),
                'label' => 'Estado',
                /*'expanded' => true,*/
                'required'    => true
            ))
            
            ->add('ciudad', TextType::class, array(
                'label' => 'Ciudad:',
                'required' => true,
                    'attr' => array(
                        'class' => 'form-control form-control-sm',
                        'placeholder'=>'ciudad'
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
            'data_class' => 'AdministradorBundle\Entity\Usuario'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'impuestos_administradorbundle_usuario';
    }
}
