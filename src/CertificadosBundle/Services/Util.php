<?php
namespace CertificadosBundle\Services;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\HttpFoundation\Session\Session;
use CertificadosBundle\Entity\LogAccesos;
class Util {

    /**
     * Contenedor de servicios
     */
    private $container;

    /**
     * Costructor de la clase
     */
    public function __construct($container) {
        $this->container = $container;
    }

    /**
     * Registra en la tabla de logs las 
     * actividades de los usuarios.
     * @param string $actividad Actividad que realizo el usuario
     * @param string $rol Rol del usuario
     */
    public function registralog($actividad,$sesion) {

        $sesion = $this->container->get('session');
        $em = $this->container->get('Doctrine')->getManager();
        $conexion = $em->getConnection();
        $fecha = date('Y-m-d H:i:s');
        $sql = " INSERT INTO log_accesos(fecha, usuario, nit, actividad) VALUES ('".$fecha."','".$sesion->get('razonSocial')."','".$sesion->get('nit')."','".$actividad."'); ";

        $conexion->query($sql);

    }
    /**
     * Valida la sesion del usuario
     * @author Alejandro Ardila Ardila <alejandro@waplicaciones.co>
     */
    public function validarSesion($sesion){
        if (time() - $sesion->get('tiempo') > 1080) {
            $sesion->invalidate();
            return false;
        }else{
            $sesion->set('tiempo',time());
            return true;
        }
    }

    public function dateEspanol($data){
        $date = explode('/', $data);
        $dia = $date[0];
        $meses=array("01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio","07"=>"Julio","08"=>"Agosto","09"=>"Setiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
        $mes = $date[1];
        $ano = $date[2];
        
        return $dia.' '.substr($meses[$mes], 0,3).' '.$ano;
    }

    public function remplazartildes($cadena){
        $replace = array('Á','É','Í','Ó','Ú');
        $arr = array('&Aacute;','&Eacute;','&Iacute;','&Oacute;','&Uacute;');
        return( str_replace($replace,$arr,$cadena));
    }

    function diferenciaEnDias($fecha_principal, $fecha_secundaria){
        $f0 = strtotime($fecha_principal);
        $f1 = strtotime($fecha_secundaria);
        //formateamos las fechas a segundos tipo 1374998435
        $diferencia = strtotime($fecha_principal) - strtotime($fecha_secundaria );
        $tiempo = $diferencia / (60 * 60 * 24);
        return $tiempo;
    }
}
?>