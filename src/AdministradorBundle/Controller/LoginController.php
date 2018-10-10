<?php
namespace AdministradorBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use AdministradorBundle\Entity\Admin;

class LoginController extends Controller
{
   public function loginAction(Request $request ){
  
        $session = $request->getSession();
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('AdministradorBundle:Default:login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'provBloqueado' => null
        ));
    }

    public function logoutAction(Request $request) {
        $session = $request->getSession();
        $session->invalidate();
        return $this->redirect($this->generateUrl('administrador_default_login'));
    }
    /**
     * Muestra la pagina principal login
     * @author Alejandro Ardila Ardila <alejandro@waplicaciones.co>
     * @return mixed template login
    */
    public function indexAction(Request $request){
        $error='';
        if($request->getMethod() == 'POST'){
            $em = $this->getDoctrine()->getManager();
            $usuario = $request->get('_username');
            $clave = $request->get('_password');
            if($usuario!="" and $clave){
                $user = $em->getRepository('AdministradorBundle:Admin')->findOneBy(array('username' => $usuario, 'password' => $clave));
                $session = $request->getSession();
                if($user){
                    $session->set('email', $user->getEmail());
                    $session->set('permiso', true);
                    $session->set('admin', true);
                    $session->set('tiempo', time());
                    return $this->redirect($this->generateUrl('admin'));
                }else{
                    $error="Usuario y/o Clave no corresponden.";
                }
            }else{
                $error='Ingrese un Usuario y una Clave';
            }
        }
        return $this->render('AdministradorBundle:Default:index.html.twig',array('error'=>$error));
    }
    
    public function reminderAction(Request $request)
    {
        if($request->getMethod() == 'POST'){
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            
            $em = $this->getDoctrine()->getManager();
            $administrador = $em->getRepository('AdministradorBundle:Admin')->findOneBy(array('email'=>$request->get('email')));
            if($administrador){
                $message = new \Swift_Message();
                $image_logo = $message->embed(\Swift_Image::fromPath('images/logoCopidrogas.png'));
                $message->setSubject('Recordar contraseña - Certificados de Retención')
                    ->setFrom($this->container->getParameter('administrador_email'),$this->container->getParameter('administrador_nombre'))
                    ->setTo($administrador->getEmail(),$administrador->getNombre())
                    ->setPriority(1)
                    ->setContentType('text/html');
                $template= $this->renderView('AdministradorBundle:Default:mail.html.twig',
                                array('password' => $administrador->getPassword(),
                                    'access' => $administrador->getUsername(),
                                    'username' => $administrador->getUsername(),
                                    'logo'=>$image_logo));
                $message->setBody($template);
                
                try{
                    $this->get('mailer')->send($message);
                    $enviado = true; //Enviado
                    $data = array(
                        'success' => true,
                        'mail' => $administrador->getEmail()
                    );
                }catch(\Exception $e){
                    $enviado = false; //No enviado
                    $data = array(
                    'success' => false,
                    'mail' => $administrador->getEmail()
                    );
                }
                 //return $this->redirect($this->generateUrl('administrador_login'));
            }else{
                $data = array(
                    'success' => false,
                    'mail' => 'Administrador no exixte'
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