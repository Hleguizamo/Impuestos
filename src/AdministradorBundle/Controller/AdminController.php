<?php
namespace AdministradorBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AdministradorBundle\Entity\Admin;
use AdministradorBundle\Entity\PermisosAdmin;
use AdministradorBundle\Form\AdminType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Admin controller.
 *
 */
class AdminController extends Controller
{
    public function permisosAction(Request $request,$id) {
        $session = $request->getSession();
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $eliminar=$em->createQuery("DELETE FROM AdministradorBundle:PermisosAdmin pa WHERE pa.administradorId=$id ")->getResult();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $json = array();
            $json['status'] = 1;
            foreach ($request->get('permisos') as $permitir) {
                $asigPermisos=new PermisosAdmin();
                $asigPermisos->setAdministradorId($id);
                $asigPermisos->setMenuAdmin($em->getReference('AdministradorBundle:MenuAdmin',$permitir));
                $asigPermisos->setTipoPermiso($request->get('TpoPermiso_'.$permitir));
                $em->persist($asigPermisos);
                $em->flush();
            }
            //$auditoria->registralog('2.4 Edita permisos del administrador ['.$id.']',$session->get('id'));
        }else{
            $json['status'] = 2;
        }
        $response->setContent(json_encode($json));
        $em->getConnection()->close();
        return $response;
    }

    public function xmlAction(Request $request){
        
        if ($request->isXmlHttpRequest()){
            /*$session = $this->getRequest()->getSession();
            $auditoria=$this->get('auditoria');
            if(!$auditoria->validarSesion($session))
                return $this->redirect($this->generateUrl('admin_login'));*/
            $busqueda=$this->get('busqueda');
            $where=$busqueda->busqueda();
            $OrdenTipo = $request->get('sord');
            $OrdenCampo = $request->get('sidx');
            $rows = $request->get('rows');
            $pagina = $request->get('page');
            $paginacion = ($pagina * $rows) - $rows;
            $em = $this->getDoctrine()->getManager();
            $entities = $em->createQuery("SELECT a FROM AdministradorBundle:Admin a ".$where." ORDER BY ".$OrdenCampo." ".$OrdenTipo);
            $entities->setMaxResults($rows);
            $entities->setFirstResult($paginacion);
            $entities = $entities->getResult();
            $Contador=$em->createQuery("SELECT COUNT(a.id) AS contador FROM AdministradorBundle:Admin a  ".$where)->getSingleResult();
            $numRegistros = $Contador['contador'];
            $totalPagina = ceil($numRegistros / $rows);
            $estados = array('0' => 'Inactivo', '1' => 'Activo');
            $response = new Response();
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/xml');
            $em->getConnection()->close();
            return $this->render('AdministradorBundle:Administrador:index.xml.twig', array('entities' => $entities,'numRegistros' => $numRegistros,
                'maxPagina' => $totalPagina,'pagina' => $pagina,'estados' => $estados), $response);
        }
    }

    /**
     * Lists all Admin entities.
     *
     */
    public function indexAction()
    {
        /*$session = $this->getRequest()->getSession();
        $auditoria=$this->get('auditoria');
        if(!$auditoria->validarSesion($session))
            return $this->redirect($this->generateUrl('admin_login'));*/
        
        return $this->render('AdministradorBundle:Administrador:index.html.twig', array('activo'=>'administrador'));
    }
    /**
     * Creates a new Admin entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $translator = $this->get('translator');
        $json = array();
        $json['status'] = 1;
        $entity  = new Admin();
        $form = $this->createForm(AdminType::class, $entity);
        $form->handleRequest($request);
        $frm_msj = null;
        if ($form->isValid()){
            $em->persist($entity);
            $em->flush();
            $json['url_edit'] = $this->generateUrl('administrador_edit', array('id' => $entity->getId(),'editado' => true));
        }else{
            //El formulario no es valido
            $json['status'] = 2;
            $frm_msj = 'Se eNcontraron errores en el formulario';
        }
        $json['template'] = $this->renderView('AdministradorBundle:Administrador:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'frm_msj' => $frm_msj
        ));
        $response->setContent(json_encode($json));
        return $response;
    }

    /**
    * Creates a form to create a Admin entity.
    *
    * @param Admin $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Admin $entity)
    {
        $form = $this->createForm(AdminType::class, $entity, array(
            'action' => $this->generateUrl('administrador_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Admin entity.
     *
     */
    public function newAction()
    {
        /*$session = $this->getRequest()->getSession();
        $auditoria=$this->get('auditoria');
        if(!$auditoria->validarSesion($session))
            return $this->redirect($this->generateUrl('admin_login'));*/
        $entity = new Admin();
        $form   = $this->createCreateForm($entity);

        return $this->render('AdministradorBundle:Administrador:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),'activo'=>'administrador'
        ));
    }

    /**
     * Finds and displays a Admin entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AdministradorBundle:Admin')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Admin entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $em->getConnection()->close();
        return $this->render('AdministradorBundle:Administrador:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(), 'activo'=>'administrador'       ));
    }

    /**
     * Displays a form to edit an existing Admin entity.
     *
     */
    public function editAction(Request $request,$id){
        /*$auditoria=$this->get('auditoria');
        if(!$auditoria->validarSesion($session))
            return $this->redirect($this->generateUrl('admin_login'));*/
        $em = $this->getDoctrine()->getManager();
        $permisosEntities=$em->createQuery("SELECT pa.tipoPermiso AS tipo,  ma.descripcionPermiso AS titulo,ma.id AS idPermisos, pa.id AS idPermiso FROM AdministradorBundle:MenuAdmin ma LEFT JOIN AdministradorBundle:PermisosAdmin pa WITH  pa.menuAdmin = ma.id AND pa.administradorId=$id WHERE ma.id !=0")->getResult();

        $entity = $em->getRepository('AdministradorBundle:Admin')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Admin entity.');
        }

        $editForm = $this->createForm(AdminType::class,$entity);
        $deleteForm = $this->createDeleteForm($id);
        $em->getConnection()->close();
        return $this->render('AdministradorBundle:Administrador:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'permisos' => $permisosEntities,
            'idAdministrador' => $id,
            'delete_form' => $deleteForm->createView(),'activo'=>'administrador'
        ));
    }

    /**
    * Creates a form to edit a Admin entity.
    *
    * @param Admin $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Admin $entity)
    {
        $form = $this->createForm(AdminType::class, $entity, array(
            'action' => $this->generateUrl('administrador_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing Admin entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $id = $request->get('id');
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $json = array();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AdministradorBundle:Admin')->find($id);
        if (!$entity) {
                $json['status'] = 2;
        }
        $editForm = $this->createForm(AdminType::class, $entity);
        $deleteForm = $this->createDeleteForm($id);
        $editForm->HandleRequest($request);
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $json['status'] = 1;
            $json['nombre'] = $entity->getNombre();
            $json['url_edit'] = $this->generateUrl('administrador_edit', array('id' => $entity->getId(),'editado' => true));
        }
        $permisos = $em->createQuery("SELECT pa.tipoPermiso AS tipo,  ma.descripcionPermiso AS titulo,ma.id AS idPermisos, pa.id AS idPermiso FROM AdministradorBundle:MenuAdmin ma LEFT JOIN AdministradorBundle:PermisosAdmin pa WITH  pa.menuAdmin = ma.id AND pa.administradorId=$id WHERE ma.id !=0")->getResult();
        $json['template'] = $this->renderView('AdministradorBundle:Administrador:edit.html.twig', array(
                        'entity' => $entity,
                        'form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                        'editado' => $request->get('editado'),
                        'permisos' => $permisos,
                        'idAdministrador' => $id
        ));
        $response->setContent(json_encode($json));
        $em->getConnection()->close();
        return $response;
    }
    /**
     * Deletes a Admin entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $session = $request->getSession();
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $json = array();
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AdministradorBundle:Admin')->find($id);
        if (!$entity) {
                $json['status'] = 2;
                $response->setContent(json_encode($json));
                return $response;
        }
        $eliminar=$em->createQuery("DELETE FROM AdministradorBundle:PermisosAdmin pa WHERE pa.administradorId= ".$entity->getId())->getResult();
        $em->remove($entity);
        $em->flush();
        $json['status'] = 1;
        $response->setContent(json_encode($json));
        $em->getConnection()->close();
        return $response;
    }

    /**
     * Creates a form to delete a Admin entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('administrador_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('eliminar', CheckboxType::class, array(
                'label'     => 'Confirma eliminar el Administrador actual?',
                'required'=>true
            ))
            ->add('submit', SubmitType::class, array(
                'label'=>'Eliminar administrador actual',
                'attr' => array(
                    'class' => 'btn btn-danger'
                )
            ))
            ->getForm()
        ;
    }
}
