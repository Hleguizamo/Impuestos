<?php
namespace AdministradorBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdministradorBundle\Entity\Usuario;
use AdministradorBundle\Form\UsuarioType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * Usuario controller.
 *
 */
class UsuarioController extends Controller
{
    public function aprobarAsociadosAction(Request $request){
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AdministradorBundle:Usuario')->findByAprobacionCambio(1);
        if (!$entity) {
            $resp['estado'] = 2;
            $response->setContent(json_encode($resp));
            return $response;
        }
        foreach ($entity as $aprobacion) {
            if ($aprobacion->getNombreCambio()!=null) {
                $aprobacion->setNombre($aprobacion->getNombreCambio());
            }
            if ($aprobacion->getDireccionCambio()!=null) {
                $aprobacion->setDireccion($aprobacion->getDireccionCambio());
            }
            if ($aprobacion->getTelCambio()!=null) {
                $aprobacion->setTel($aprobacion->getTelCambio());
            }
            if ($aprobacion->getRepresentanteCambio()!=null) {
                $aprobacion->setRepresentante($aprobacion->getRepresentanteCambio());
            }
            if ($aprobacion->getMailCambio()!=null) {
                $aprobacion->setMail($aprobacion->getMailCambio());
            }
            if ($aprobacion->getPasswordCambio()!=null) {
                $aprobacion->setPassword($aprobacion->getPasswordCambio());
            }
            if ($aprobacion->getCiudadCambio()!=null) {
                $aprobacion->setCiudad($aprobacion->getCiudadCambio());
            }
            $aprobacion->setAprobacionCambio(2);
            $em->persist($aprobacion);
            $em->flush();
            $aprobacion->setNombreCambio(null);
            $aprobacion->setDireccionCambio(null);
            $aprobacion->setTelCambio(null);
            $aprobacion->setRepresentanteCambio(null);
            $aprobacion->setMailCambio(null);
            $aprobacion->setPasswordCambio(null);
            $aprobacion->setCiudadCambio(null);
            $em->persist($aprobacion);
            $em->flush();
            if (filter_var($aprobacion->getMail(), FILTER_VALIDATE_EMAIL)){
                $message = new \Swift_Message();
                $imageLogo = $message->embed(\Swift_Image::fromPath('images/logoCopidrogas.png'));
                $message->setSubject('Aceptacion De Cambios - Impuestos')->setFrom($this->container->getParameter('administrador_email'),$this->container->getParameter('administrador_nombre'))->setTo($aprobacion->getMail(),$aprobacion->getNombre())->setPriority(1)->setContentType('text/html');
                set_time_limit(120);
                $template= $this->renderView('AdministradorBundle:Usuario:envioACCambios.html.twig',array('entity' => $entity,'logoCopi'=>$imageLogo));
                $message->setBody($template);
                $json['status'] = 2;
                try{
                    $this->get('mailer')->send($message);
                    $json['status'] = 'Correo Enviado';
                }catch(\Exception $e){
                    $json['status'] = 'Error en el envio'; //No enviado
                }
            }else{
                $json['status'] = 'Correo no valido';
            }
        }
        $resp['estado'] = 1;
        $response->setContent(json_encode($resp));
        $em->getConnection()->close();
        return $response;
    }

    public function aprobarAsociadoAction(Request $request) {

        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AdministradorBundle:Usuario')->find($request->get('id'));

        if (!$entity) 
            throw $this->createNotFoundException('No se encontro el registro solicitado');

        $opcion = $request->get('opcion');
        //dump($entity);exit;
        if ($opcion == 'previsualizacion') {

            $html = $this->renderView('AdministradorBundle:Usuario:verDatosAAprovar.html.twig',array(
                'entity'=>$entity
            ));

            return new Response($html);


        } else {

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');

        }//end if-else


        if ($entity->getNombreCambio()!=null) {
            $entity->setNombre($entity->getNombreCambio());
        }
        if ($entity->getDireccionCambio()!=null) {
            $entity->setDireccion($entity->getDireccionCambio());
        }
        if ($entity->getTelCambio()!=null) {
            $entity->setTel($entity->getTelCambio());
        }
        if ($entity->getRepresentanteCambio()!=null) {
            $entity->setRepresentante($entity->getRepresentanteCambio());
        }
        if ($entity->getMailCambio()!=null && filter_var($entity->getMailCambio(), FILTER_VALIDATE_EMAIL)) {
            $entity->setMail($entity->getMailCambio());
        }
        if ($entity->getPasswordCambio()!=null) {
            $entity->setPassword($entity->getPasswordCambio());
        }
        if ($entity->getCiudadCambio()!=null) {
            $entity->setCiudad($entity->getCiudadCambio());
        }
        $entity->setAprobacionCambio(2);
        $em->persist($entity);
        $em->flush();
        $entity->setNombreCambio(null);
        $entity->setDireccionCambio(null);
        $entity->setTelCambio(null);
        $entity->setRepresentanteCambio(null);
        $entity->setMailCambio(null);
        $entity->setPasswordCambio(null);
        $entity->setCiudadCambio(null);
        $em->persist($entity);
        $em->flush();

        if (filter_var($entity->getMail(), FILTER_VALIDATE_EMAIL)){
            $message = new \Swift_Message();
            $imageLogo = $message->embed(\Swift_Image::fromPath('images/logoCopidrogas.png'));

            $message->setSubject('Aceptacion De Cambios - Impuestos')->setFrom($this->container->getParameter('administrador_email'),$this->container->getParameter('administrador_nombre'))->setTo($entity->getMail(),$entity->getNombre())->setPriority(1)->setContentType('text/html');

            //$message->setSubject('Aceptacion De Cambios - Impuestos')->setFrom($this->container->getParameter('administrador_email'),$this->container->getParameter('administrador_nombre'))->setTo('j.casas@waplicaciones.co','WA')->setPriority(1)->setContentType('text/html');


            set_time_limit(120);
            $template= $this->renderView('AdministradorBundle:Usuario:envioACCambios.html.twig',array('entity' => $entity,'logoCopi'=>$imageLogo));
            $message->setBody($template);
            $json['status'] = 2;
            try{
                $this->get('mailer')->send($message);
                $json['status'] = 'Correo Enviado';
            }catch(\Exception $e){
                $json['status'] = 'Error en el envio'; //No enviado
            }
        }else{
            $json['status'] = 'Correo no valido';
        }
        $resp['estado'] = 1;
        $response->setContent(json_encode($resp));
        $em->getConnection()->close();
        return $response;
    }

    public function xmlAction(Request $request){
        
        if ($request->isXmlHttpRequest()){
            $session = $request->getSession();
            $auditoria=$this->get('auditoria');
            /*$auditoria->validarSesion();
            if(!$auditoria->seguridad_http('administrador'))
                return $this->redirect($this->generateUrl('admin_login'));*/
            $busqueda=$this->get('busqueda');
            $where=$busqueda->busqueda();
            $OrdenTipo = $request->get('sord');
            $OrdenCampo = $request->get('sidx');
            $rows = $request->get('rows');
            $pagina = $request->get('page');
            $paginacion = ($pagina * $rows) - $rows;
            $em = $this->getDoctrine()->getManager();
            $entities = $em->createQuery("SELECT a FROM AdministradorBundle:Usuario a ".$where." ORDER BY ".$OrdenCampo." ".$OrdenTipo);
            $entities->setMaxResults($rows);
            $entities->setFirstResult($paginacion);
            $entities = $entities->getResult();
            $Contador=$em->createQuery("SELECT COUNT(a.id) AS contador FROM AdministradorBundle:Usuario a  ".$where)->getSingleResult();
            $numRegistros = $Contador['contador'];
            $totalPagina = ceil($numRegistros / $rows);
            $estados = array('0' => 'Inactivo', '1' => 'Activo');
            $response = new Response();
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/xml');
            $em->getConnection()->close();
            return $this->render('AdministradorBundle:Usuario:index.xml.twig', 
                array(
                    'entities' => $entities,
                    'numRegistros' => $numRegistros,
                    'maxPagina' => $totalPagina,
                    'pagina' => $pagina,
                    'estados' => $estados), 
                    $response);
        }
    }
    /**
     * Lists all Usuario entities.
     *
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $auditoria=$this->get('auditoria');
        /*if(!$auditoria->validarSesion($session))
            return $this->redirect($this->generateUrl('admin_login'));*/
        return $this->render('AdministradorBundle:Usuario:index.html.twig',array('activo'=>'proveedor'));
    }
    /**
     * Creates a new Usuario entity.
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
        $entity  = new Usuario();
        $form = $this->createForm(UsuarioType::class, $entity);
        $form->handleRequest($request);
        $frm_msj = null;


        if ($form->isValid()){
            $entity->setCambioClave(new \DateTime());
            $em->persist($entity);
            $em->flush();
            $json['url_edit'] = $this->generateUrl('usuario_edit', array('id' => $entity->getId(),'editado' => true));
        }else{
            //El formulario no es valido
            $json['status'] = 2;
            $frm_msj = 'Se encontraron errores en el formulario';
        }
        $json['template'] = $this->renderView('AdministradorBundle:Usuario:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'frm_msj' => $frm_msj
        ));
        $response->setContent(json_encode($json));
        $em->getConnection()->close();
        return $response;
    }

    /**
    * Creates a form to create a Usuario entity.
    *
    * @param Usuario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    /*
    private function createCreateForm(Usuario $entity)
    {
        $form = $this->createForm(UsuarioType::class, $entity, array(
            'action' => $this->generateUrl('usuario_create'),
            'method' => 'POST',
        ));
        return $form;
    }
    */
    /**
     * Displays a form to create a new Usuario entity.
     *
     */
    public function newAction(Request $request)
    {
        $session = $request->getSession();
        $auditoria=$this->get('auditoria');
        /*if(!$auditoria->validarSesion($session))
            return $this->redirect($this->generateUrl('admin_login'));*/
        $entity = new Usuario();
        $form   = $this->createForm(UsuarioType::class,$entity);

        return $this->render('AdministradorBundle:Usuario:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),'activo'=>'proveedor'
        ));
    }

    /**
     * Finds and displays a Usuario entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AdministradorBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $em->getConnection()->close();
        return $this->render('AdministradorBundle:Usuario:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),  'activo'=>'proveedor'      ));
    }

    /**
     * Displays a form to edit an existing Usuario entity.
     *
     */
    public function editAction(Request $request,$id)
    {
        $session = $request->getSession();
        $auditoria=$this->get('auditoria');
        /*if(!$auditoria->validarSesion($session))
            return $this->redirect($this->generateUrl('admin_login'));*/
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AdministradorBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        $em->getConnection()->close();
        return $this->render('AdministradorBundle:Usuario:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),'activo'=>'proveedor'
        ));
    }

    /**
    * Creates a form to edit a Usuario entity.
    *
    * @param Usuario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Usuario $entity)
    {
        $form = $this->createForm(UsuarioType::class, $entity, array(
            'action' => $this->generateUrl('usuario_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing Usuario entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $id = $request->get('id');
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $json = array();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AdministradorBundle:Usuario')->find($id);
        
        if (!$entity) {
                $json['status'] = 2;
        }
        $editForm = $this->createForm(UsuarioType::class, $entity);
        $deleteForm = $this->createDeleteForm($id);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $json['status'] = 1;
            $json['nombre'] = $entity->getNombre();
            $json['url_edit'] = $this->generateUrl('usuario_edit', array('id' => $entity->getId(),'editado' => true));
        }
        $json['template'] = $this->renderView('AdministradorBundle:Usuario:edit.html.twig', array(
                        'entity' => $entity,
                        'form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                        'editado' => $request->get('editado')
        ));
        $response->setContent(json_encode($json));
        $em->getConnection()->close();
        return $response;
    }
    /**
     * Deletes a Usuario entity.
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
        $entity = $em->getRepository('AdministradorBundle:Usuario')->find($id);
        if (!$entity) {
                dump("Llego"); exit;
                $json['status'] = 2;
                $response->setContent(json_encode($json));
                return $response;
        }
        $em->remove($entity);
        $em->flush();
        $json['status'] = 1;
        $response->setContent(json_encode($json));
        $em->getConnection()->close();
        return $response;
    }

    /**
     * Creates a form to delete a Usuario entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usuario_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('eliminar', CheckboxType::class, array(
                'label'     => 'Confirma eliminar el Proveedor actual?',
                'required'=>true
            ))
            ->add('submit', SubmitType::class, array(
                'label'=>'Eliminar Proveedor actual',
                'attr' => array(
                    'class' => 'uk-button uk-button-danger'
                )
            ))
            ->getForm()
        ;
    }

    public function cargarArchivoAction(Request $request) {
        return $this->render('AdministradorBundle:Usuario:cargaArchivo.html.twig', array('activo'=>'proveedor'));
    }

    public function subirArchivoAction(Request $request) {
        $session = $request->getSession();
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        if ($request->getMethod() == 'POST') {
                set_time_limit(5000);
                ini_set('memory_limit', '512M');
                $em = $this->getDoctrine()->getManager();
                $proveedor=$em->getRepository('AdministradorBundle:Usuario')->findAll();
                $nitsProveedor=array();
                foreach ($proveedor as $idProveedor) {
                    $nitsProveedor[$idProveedor->getNit()]=$idProveedor->getNit();
                }
                $con = $em->getConnection();
                $file = $request->files->get('cargarProveedor');
                $pathUpload='./uploads/';
                $file->move('./uploads/','proveedor.xls');
                $filename = $pathUpload.'proveedor.xls';
                $excelObj = \PHPExcel_IOFactory::load($filename);
        
                $excelObj->setActiveSheetIndex(0);
                $nitsProveedorComprobar=array();
                $sheetDataPedido = $excelObj->getActiveSheet()->toArray(null,true,true,true);
                $fecha = date('Y-m-d');
                $nuevafecha = strtotime ( '-4 month' , strtotime ( $fecha ) ) ;

                $insertProveedor='INSERT INTO usuario(nit, nombre, direccion, tel, representante, mail, password, enabled, ciudad, cambio_clave) VALUES ';                
                $sql=$resultados="";
                $NumeroRegistros=$actualizados=$creados=$contador=0;
                foreach($sheetDataPedido as $key => $row) {
                    if ($key>1) {
                        if (!isset($nitsProveedorComprobar[$row['A']])) {
                            $nombre= isset($row['B'])? $row['B'] : null;
                            $direccion= isset($row['C'])? $row['C'] : null;
                            $telefono= isset($row['D'])? $row['D'] : null;
                            $representante= isset($row['E'])? $row['E'] : null;
                            $email= isset($row['F'])? $row['F'] : null;
                            $ciudad= isset($row['G'])? $row['G'] : null;
                            if($request->get('opcionCarga')==1){
                                $em->createQuery('DELETE FROM AdministradorBundle:Usuario u')->execute();
                                if($row['A']!=''){
                                    $resultados.="El proveedor con nit : <strong>".$row['A']."</strong> ha sido creado<br>";
                                    if($contador > 0)
                                        $sql.=",";                
                                    $sql .= " ('".$row['A']."', '".$nombre."', '".$direccion."', '".$telefono."', '".$representante."', '".$email."', '".$row['A']."',1,'".$ciudad."', '".date('Y-m-d' ,$nuevafecha)."')";
                                    $contador++;
                                    $creados++;
                                    if($contador==200){
                                        $con->query($insertProveedor.$sql);$sql="";
                                        $contador=0;
                                    }
                                }
                            }
                            if ($request->get('opcionCarga')==2) {
                                if (isset($nitsProveedor[$row['A']])) {
                                    $em->createQuery('UPDATE AdministradorBundle:Usuario u 
                                                        SET u.nombre=:nombre, u.direccion=:direccion, u.tel=:telefono,
                                                         u.representante=:representante,u.mail=:email,
                                                         u.ciudad=:ciudad WHERE u.nit=:nit')
                                        ->setParameter('nit', $row['A'])
                                        ->setParameter('nombre', isset($row['B'])? $row['B'] : null)
                                        ->setParameter('direccion', isset($row['C'])? $row['C'] : null)
                                        ->setParameter('telefono', isset($row['D'])? $row['D'] : null)
                                        ->setParameter('representante', isset($row['E'])? $row['E'] : null)
                                        ->setParameter('email', isset($row['F'])? $row['F'] : null)
                                        ->setParameter('ciudad', isset($row['G'])? $row['G'] : null)
                                        ->execute();
                                    $actualizados++;
                                    $resultados.="El proveedor con nit : <strong>".$row['A']."</strong> se actualizo<br>";
                                }else{
                                    if($row['A']!=''){
                                        $resultados.="El proveedor con nit : <strong>".$row['A']."</strong> ha sido creado<br>";
                                        if($contador > 0)
                                            $sql.=",";                
                                        $sql .= " ('".$row['A']."', '".$nombre."', '".$direccion."', '".$telefono."', '".$representante."', '".$email."', '".$row['A']."',1,'".$ciudad."', '".date('Y-m-d' ,$nuevafecha)."')";
                                        $contador++;
                                        $creados++;
                                        if($contador==200){
                                            $con->query($insertProveedor.$sql);$sql="";
                                            $contador=0;
                                        }
                                    }
                                }
                            }
                            if ($request->get('opcionCarga')==3) {
                                if (!isset($nitsProveedor[$row['A']])) {
                                    if($row['A']!=''){
                                        $resultados.="El proveedor con nit : <strong>".$row['A']."</strong> ha sido creado<br>";
                                        if($contador > 0)
                                            $sql.=",";                
                                        $sql .= " ('".$row['A']."', '".$nombre."', '".$direccion."', '".$telefono."', '".$representante."', '".$email."', '".$row['A']."',1,'".$ciudad."', '".date('Y-m-d' ,$nuevafecha)."')";
                                        $contador++;
                                        $creados++;
                                        if($contador==200){
                                            $con->query($insertProveedor.$sql);$sql="";
                                            $contador=0;
                                        }
                                    }
                                }else{
                                    $resultados.="El proveedor con nit : <strong>".$row['A']."</strong>, ya existe <br>";
                                }
                            }
                            $NumeroRegistros++;
                            $nitsProveedorComprobar[$row['A']]=$row['A'];
                        } 
                    } 
                }
                if($contador>0){
                    $con->query($insertProveedor.$sql.";");$sql="";
                }
                $resp['estado'] = 1;
                $resp['resultados'] = 'Numero de registros creados: <strong>'.$creados.'/'.$NumeroRegistros.'</strong><br>'.'Numero de registros actualizados: <strong>'.$actualizados.'/'.$NumeroRegistros.'</strong><br><strong>DETALLE</strong><br>'.$resultados;
                $response->setContent(json_encode($resp));
                $em->getConnection()->close();
                return $response;
        }  else {
            throw $this->createNotFoundException('Metodo no valido1.');
        }
        exit();
    }
}
