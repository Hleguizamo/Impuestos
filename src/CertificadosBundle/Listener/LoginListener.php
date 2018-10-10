<?php

namespace CertificadosBundle\Listener;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use CertificadosBundle\Entity\Usuario;

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

    }
    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from AbstractAuthenticationListener.
     * @param Request        $request
     * @param TokenInterface $token
     * @return Response The response to return
     */
    function onAuthenticationSuccess(Request $request, TokenInterface $token){
        //dump($token->getUser());exit;
        $auditoria=$this->container->get('auditoria');

        if ($token->getUser()->getEnabled() == 1) {

            $session = $request->getSession();
            $session->set('mail', $token->getUser()->getMail());
            $session->set('razonSocial', $token->getUser()->getNombre());
            $session->set('nit', $token->getUser()->getNit());
            $session->set('id', $token->getUser()->getId());
            $session->set('tiempo', time());
            $uri = $this->container->get('router')->generate('usuario_usuario');

            if ($auditoria->diferenciaEnDias(date("Y-m-d"),$token->getUser()->getCambioClave()->format('Y-m-d'))>=90) 
                $uri = $this->container->get('router')->generate('usuario_cambioClave');
            
        } else {

            $uri = $this->container->get('router')->generate('usuario_login');

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

        $usuario = $request->request->get('_username');
        $referer = $request->headers->get('referer');
        $request->getSession()->getFlashBag()->add('error', $exception->getMessage());

        return new RedirectResponse($referer);

    }


    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @param Request $request
     *
     * @return Response never null
     */
    public function onLogoutSuccess(Request $request){
        dump('autenticado');exit;
        if ($this->container->get('security.authentication_utils')->getToken()){
            $session=$request->getSession();
            $session->set('token',false);
            $util=$this->container->get('utilidadesAsociado');
            $util->registralog('1.6 Finaliza sesion. ',$session->get('nit'),$session->get('asociado'));         
            $session->invalidate();
        }
        $uri = $this->container->get('router')->generate('asociado_default_login');
        $response = new RedirectResponse($uri);

        return $response;

    }//end function

    private function retornaBloqueo($actual)
    {
        $Bloqueo[0][0]="01";
        $Bloqueo[0][1]="Insuficiencia cupo";
        $Bloqueo[1][0]="02";
        $Bloqueo[1][1]="Mora en el pago";
        $Bloqueo[2][0]="03";
        $Bloqueo[2][1]="Sanci&oacute;n";
        $Bloqueo[3][0]="04";
        $Bloqueo[3][1]="Cheque devuelto";
        $Bloqueo[4][0]="05";
        $Bloqueo[4][1]="Ahorro y Cr&eacute;dito";
        $Bloqueo[5][0]="06";
        $Bloqueo[5][1]="Pliego de cargos";
        $Bloqueo[6][0]="07";
        $Bloqueo[6][1]="Venta droguer&iacute;a";
        $Bloqueo[7][0]="08";
        $Bloqueo[7][1]="Otros motivos";
        $Bloqueo[8][0]="09";
        $Bloqueo[8][1]="Fallecimiento";
        $Bloqueo[9][0]="10";
        $Bloqueo[9][1]="Juridico";
        $Bloqueo[10][0]="50";
        $Bloqueo[10][1]="Suspendida";
        $Bloqueo[11][0]="51";
        $Bloqueo[11][1]="Bloqueada";
        $Bloqueo[12][0]="52";
        $Bloqueo[12][1]="Retirada";
        $Bloqueo[13][0]="53";
        $Bloqueo[13][1]="Retirada Fallecido";
        $Bloqueo[14][0]="54";
        $Bloqueo[14][1]="Retirada Traslados";
        $Bloqueo[15][0]="55";
        $Bloqueo[15][1]="Retirada Exclusiones";
        $Bloqueo[16][0]="56";
        $Bloqueo[16][1]="Retirada Forzoso";
        $Bloqueo[17][0]="99";
        $Bloqueo[17][1]="test(e)";
        foreach($Bloqueo as $bloqueo)
        {
            if($bloqueo[0]==$actual)
            return "Bloqueado por ".$bloqueo[1];
        }
        return "Activado";
    }

}//end class
