<?php
namespace AdministradorBundle\Listener;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Impuestos\AdministradorBundle\Entity\Admin;

/**
 * Custom authentication success handler
 */
class LoginListener implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface {

    private $container;
    private $em;

    /**
     * Constructor
     * @param container   $container
     */
    public function __construct($container){
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        /*
        $em= $this->container->get('doctrine')->getManager();
        $admin=$em->getRepository('SIPAdministradorBundle:Administradores')->findAll();
        var_dump($admin);exit();
        */
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from AbstractAuthenticationListener.
     * @param Request        $request
     * @param TokenInterface $token
     * @return Response The response to return
     */
    function onAuthenticationSuccess(Request $request, TokenInterface $token){
            $session = $request->getSession();
            $administrador = $this->em->getRepository('AdministradorBundle:Admin')->findOneById($token->getUser()->getId());
           
            $permisosEntities=$this->em->createQuery("SELECT pa.tipoPermiso AS tipo,  ma.descripcionPermiso AS titulo FROM AdministradorBundle:PermisosAdmin pa JOIN pa.menuAdmin ma WHERE pa.administradorId=".$token->getUser()->getId())->getResult();
            foreach ($permisosEntities as $permiso){
                    $Permitidos[$permiso['titulo']]['tipo']=$permiso['tipo'];
            }
            $session->set('administrador', $token->getUser()->getNombre());
            $session->set('permisos', $Permitidos);
            $session->set('id', $token->getUser()->getId());
            if (isset($Permitidos['administradores'])) {
                $uri = $this->container->get('router')->generate('administrador');
            }elseif (isset($Permitidos['proveedores'])) {
                $uri = $this->container->get('router')->generate('usuario');
            }elseif (isset($Permitidos['actualizar datos'])) {
                $uri = $this->container->get('router')->generate('admin_loaddata');
            }elseif (isset($Permitidos['registro actividades'])) {
                $uri = $this->container->get('router')->generate('log');
            }else{
                $uri = $this->container->get('router')->generate('administrador_homepage');
            }
            return new RedirectResponse($uri);
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception){

        $uri = $this->container->get('router')->generate('admin_loginError',array('sin_autenticar'=>true));
        $request->getSession()->getFlashBag()->add('error', $exception->getMessage());
        return new RedirectResponse($uri);

    }

    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @param Request $request
     *
     * @return Response never null
     */
    public function onLogoutSuccess(Request $request)
    {
        if ($this->container->get('security.context')->getToken())
        {
            $usuario = $this->container->get('security.context')->getToken()->getUser();
            /*$log = $this->container->get('Util');
            $log->registralog('(PRPROV2) Sesión de usuario cerrada', $usuario->getId());*/
        }

        $uri = $this->container->get('router')->generate('administrador_homepage');
        $response = new RedirectResponse($uri);
        return $response;
    }

}
