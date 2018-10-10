<?php

namespace CertificadosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use CertificadosBundle\Entity\Usuario;

class DefaultController extends Controller
{
    public function loginAction(Request $request){
        $session = $request->getSession();
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('CertificadosBundle:Default:index.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'provBloqueado' => null
        ));
    }

    public function indexAction(Request $request) {

        $session = $request->getSession();

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('CertificadosBundle:Default:index.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'provBloqueado' => null
        ));
        
    }


    public function logoutAction(Request $request) {

        $session = $request->getSession();
        $auditoria=$this->get('auditoria');
        $auditoria->registralog('Cierra sesion',$session);
        $session->clear();
        $session->invalidate();

        return $this->redirect($this->generateUrl('usuario_login'));

    }

    public function cambioClaveAction(Request $request){
     
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $afiliado = $em->getRepository('CertificadosBundle:Usuario')->findOneBy(array('id' => $session->get('id')));
        
        return $this->render('CertificadosBundle:Default:cambioClaveVencida.html.twig', array(
            'data' => $afiliado,
        ));
    }

    public function cambiarClaveAction(Request $request) {
        //echo "Si llega0";
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $json = array();
        $json['status'] = 1;
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $administrador = $em->getRepository('CertificadosBundle:Usuario')->findOneBy(array('id' => $session->get('id')));

        $clave_actual = $request->get('clave_actual');
        $data['status']=1;
        if ($this->buscar_clave_actual($request,$clave_actual)) {
            $clave = $request->get('nuevaClave');
            $administrador->setPassword($clave);
            $administrador->setCambioClave(new \DateTime("now"));
            $em->persist($administrador);
            $em->flush();
            $auditoria = $this->get('auditoria');
            $auditoria->registralog('Cambio de clave de acceso al aplicativo',$session->get('id'));
            $data['login'] = $this->generateUrl('usuario_logout');
        }else{
            $data['status']=2;
        }
        $response->setContent(json_encode($data));
        return $response;
    }
    
    
    private function buscar_clave_actual(Request $request,$clave_actual) {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $clave_actual_proveedor = $em->getRepository('CertificadosBundle:Usuario')->findOneBy(array('id' => $session->get('id')))->getPassword();
        if ($clave_actual_proveedor == $clave_actual) {
            return true;
        } else {
            return false;
        }
    }
    public function envioAction(Request $request)
    {
        $message = new \Swift_Message();
            //$imageLogo = $message->embed(\Swift_Image::fromPath('images/sipAsociados.gif'));
            //$imageLogo2 = $message->embed(\Swift_Image::fromPath('images/logoCopidrogas.png'));
            $from = 'alejandro@waplicaciones.co';
            $name = 'Alejandro Ardila Ardila';
            $message->setSubject('Prueba de Envio')->setFrom('alejandro@waplicaciones.co','Impuestos')->setTo($from,$name)->setPriority(1)->setContentType('text/html');
            set_time_limit(120);
            
            $message->addBcc('desarrollo@waplicaciones.net','Alejandro Ardila Ardila');
            $template= 'Respuesta';
            $message->setBody($template);
            $this->get('mailer')->send($message);
            exit;
        }

    public function reminderAction(Request $request)
    {
        if($request->getMethod() == 'POST'){
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository('CertificadosBundle:Usuario')->findOneBy(array('nit'=>$request->get('nit')));
            if($usuario){
                $message = new \Swift_Message();
                $image_logo = $message->embed(\Swift_Image::fromPath('images/logoCopidrogas.png'));
                $message->setSubject('Recordar contraseña - Certificados de Retención')
                    ->setFrom($this->container->getParameter('administrador_email'),$this->container->getParameter('administrador_nombre'))
                    ->setTo($usuario->getMail(),$usuario->getNombre())
                    ->setContentType('text/html');
                $template=$this->renderView('CertificadosBundle:Default:mail.html.twig',array('password' => $usuario->getPassword(),'access' => $usuario->getNombre(),'username' => $usuario->getNit(),'logo'=>$image_logo));
                $message->setBody($template);
                
                try{

                    $this->get('mailer')->send($message);
                    $data = array(
                        'success' => true,
                        'mail' => $usuario->getMail()
                    );
                }catch(\Exception $e){
                    $data = array(
                        'success' => false,
                        'mail' => $usuario->getMail()
                    );
                }
            }else{
                $data = array(
                    'success' => false,
                    'nit' => 'Usuario no encontrado'
                );
            }
            $response->setContent(json_encode($data));
            return $response;
        }
        else{
            throw $this->createNotFoundException('Imposible responder a esta peticion. falta el POST');
        }
    }

}
