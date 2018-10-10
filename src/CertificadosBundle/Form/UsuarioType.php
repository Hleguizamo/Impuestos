<?php

namespace CertificadosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UsuarioType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nit',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control',
                    'disabled'=>true),
                'empty_data' => $options['data']->getNit()))

            ->add('nombre',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control',
                    'disabled'=>true),
                'empty_data' => $options['data']->getNombre()))

            ->add('direccion',TextType::class,
                array('attr'=>array(
                    'class' => 'form-control',
                    'disabled'=>true),
                'empty_data' => $options['data']->getDireccion()))

            ->add('tel',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control',
                    'disabled'=>true),
                'empty_data' => $options['data']->getTel()))

            ->add('representante',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control',
                    'disabled'=>true),
                'empty_data' => $options['data']->getRepresentante()))

            ->add('mail',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control',
                    'disabled'=>true),
                'empty_data' => $options['data']->getMail()))

            ->add('password',PasswordType::class,array(
                'attr'=>array(
                    'class' => 'form-control',
                    'disabled'=>true),
                'empty_data' => $options['data']->getPassword()))

            ->add('ciudad',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control',
                    'disabled'=>true),
                'empty_data' => $options['data']->getCiudad()))

            ->add('nombreCambio',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control'), 
                'label'=>'Nuevo Nombre',
                'required'=>false))

            ->add('direccionCambio',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control'),
                'label'=>'Nueva Dirección',
                'required'=>false))

            ->add('telCambio',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control'),
                'label'=>'Nuevo Teléfono',
                'required'=>false))

            ->add('representanteCambio',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control'),
                'label'=>'Nuevo Representante',
                'required'=>false))

            ->add('mailCambio',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control'),
                'label'=>'Nuevo Email',
                'required'=>false))
            
            ->add('passwordCambio',PasswordType::class,array(
                'attr'=>array(
                    'class' => 'form-control'),
                'label'=>'Nueva Contraseña',
                'required'=>false))

            ->add('ciudadCambio',TextType::class,array(
                'attr'=>array(
                    'class' => 'form-control'),
                'label'=>'Nueva Ciudad',
                'required'=>false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Impuestos\CertificadosBundle\Entity\Usuario'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'impuestos_certificadosbundle_usuario';
    }
}
