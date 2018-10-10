<?php
namespace AdministradorBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdministradorBundle\Entity\LogAccesos;
/**
 * Admin controller.
 *
 */
class LogController extends Controller
{
    public function xmlAction(Request $request){
        
        if ($request->isXmlHttpRequest()){
            $session = $request->getSession();
            $auditoria=$this->get('auditoria');
            /*if(!$auditoria->validarSesion($session))
                return $this->redirect($this->generateUrl('admin_login'));*/
            $busqueda=$this->get('busqueda');
            $where=$busqueda->busqueda();
            $OrdenTipo = $request->get('sord');
            $OrdenCampo = $request->get('sidx');
            $rows = $request->get('rows');
            $pagina = $request->get('page');
            $paginacion = ($pagina * $rows) - $rows;
            $em = $this->getDoctrine()->getManager();
            $entities = $em->createQuery("SELECT LA FROM AdministradorBundle:LogAccesos LA ".$where." ORDER BY ".$OrdenCampo." ".$OrdenTipo);
            $entities->setMaxResults($rows);
            $entities->setFirstResult($paginacion);
            $entities = $entities->getResult();
            $Contador=$em->createQuery("SELECT COUNT(LA.id) AS contador FROM AdministradorBundle:LogAccesos LA  ".$where)->getSingleResult();
            $numRegistros = $Contador['contador'];
            $totalPagina = ceil($numRegistros / $rows);
            $response = new Response();
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/xml');
            return $this->render('AdministradorBundle:Log:index.xml.twig', array('entities' => $entities,'numRegistros' => $numRegistros,'maxPagina' => $totalPagina,'pagina' => $pagina), $response);
        }
    }
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $auditoria=$this->get('auditoria');
        /*if(!$auditoria->validarSesion($session))
            return $this->redirect($this->generateUrl('admin_login'));*/
        return $this->render('AdministradorBundle:Log:index.html.twig', array('activo'=>'log'));
    }
}
