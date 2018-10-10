<?php
namespace CertificadosBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CertificadosBundle\Entity\Usuario;
use CertificadosBundle\Form\UsuarioType;

/**
 * Usuario controller.
 *
 * @Route("/usuario")
 */
class UsuarioController extends Controller{

    /**
     * Lists all Usuario entities.
     *
     * @Route("/", name="usuario")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {

        $session = $request->getSession();
        $auditoria=$this->get('auditoria');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CertificadosBundle:Usuario')->findByNit($session->get('nit'));
        $certificadoIca=$em->getRepository('CertificadosBundle:DataIca')->findOneByNit($session->get('nit'));
        $certificadoIva=$em->getRepository('CertificadosBundle:DataIva')->findOneByNit($session->get('nit'));
        $certificadoRenta=$em->getRepository('CertificadosBundle:DataRenta')->findOneByNit($session->get('nit'));

        return $this->render('CertificadosBundle:Usuario:index.html.twig', array(
            'users'  =>$user,
            'certificadoIca' => $certificadoIca,
            'certificadoIva' => $certificadoIva,
            'certificadoRenta' => $certificadoRenta
        ));
    }

    
    public function editarAction(Request $request){

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CertificadosBundle:Usuario')->findOneByNit($session->get('nit'));
        if (!$entity) {
                throw $this->createNotFoundException('Unable to find Usuario entity.');
        }
        $editForm = $this->createForm(UsuarioType::class, $entity);
        return $this->render('CertificadosBundle:Usuario:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'editado' => $request->get('editado')
        ));
    }

    public function updateAction(Request $request, $id){

        $session = $request->getSession();
        $auditoria=$this->get('auditoria');
        $json = array();
        $json['status'] = 2;

        if($request->isXmlHttpRequest()){

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
  
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CertificadosBundle:Usuario')->find($id);

            if (!$entity) 
                $json['status'] = 2;
            
            $editForm = $this->createForm(UsuarioType::class, $entity);
            $editForm->handleRequest($request);           


            if ($editForm->isValid()) {

                if ($request->get('repetirNuevaClave') !='' && $request->get('repetirNuevaClave') != null)
                    $entity->setPassword($request->get('repetirNuevaClave'));

                $entity->setAprobacionCambio(1);
                $em->persist($entity);
                $em->flush();

                $message = new \Swift_Message();
                $imageLogo = $message->embed(\Swift_Image::fromPath('images/logoCopidrogas.png'));

                $message->setSubject('Solicitud Aprobación De Cambios - Impuestos(caso omiso)')->setFrom($this->container->getParameter('administrador_email'),$this->container->getParameter('administrador_nombre'))->setTo($this->container->getParameter('administrador_email'),$this->container->getParameter('administrador_nombre'))->setPriority(1)->setContentType('text/html');
                set_time_limit(120);
                $template= $this->renderView('CertificadosBundle:Usuario:envioSolicitudCambios.html.twig',array('entity' => $entity,'logoCopi'=>$imageLogo));
                $message->setBody($template);
                $json['status'] = 2;

                try{
                    $this->get('mailer')->send($message);
                    $json['status'] = 'Correo Enviado';
                }catch(\Exception $e){
                    $json['status'] = 'Error en el envio'; //No enviado
                }
              
                $auditoria = $this->get('auditoria');
                $auditoria->registralog('1.2 El proveedor edita datos ['.$entity->getId().' - '.$entity->getNombre().']',$session->get('id'));

                $json['status'] = 1;
                $json['nombre'] = $entity->getNombre();

                $json['url_edit'] = $this->generateUrl('usuario_actualizarUsuario', array('id' => $entity->getId(),'editado' => true));
            }

            $json['template'] = $this->renderView('CertificadosBundle:Usuario:edit.html.twig', array(
                            'entity' => $entity,
                            'form' => $editForm->createView(),
                            'editado' => $request->get('editado'),
            ));

           
            $response->setContent(json_encode($json));
            return $response;

        }else{
            return new Response('Imposible responder a su solicitud');
        }
    }


    
    private function getYearsFilter(){
        $date = array();
        for($i=date('Y'); $i>=2013 ;$i--){
                array_push($date,$i);

        }
        return $date;
    }
	
    public function icaAction(){
            $data = array(
        'success' => true,
        'root' => 'people'
    );

            return new JsonResponse(
                    $data
            );
    }
	
	public function yearAction($val){
		$request = $this->get('request_stack')->getCurrentRequest();
		$sessiondata = $request->getSession();
		$sessiondata->set('year', $val);
		$data = array('year' => $sessiondata->get('year'));
            return new JsonResponse(
                $data
            );
	}
	
	public function activitiesAction(Request $request,$type){
		$session = $request->getSession();
                $auditoria=$this->get('auditoria');
                /*if(!$auditoria->validarSesion($session))
                    return $this->redirect($this->generateUrl('usuario_login'));*/
		$data = array();
		
		if($type == 'iva'){
		}
		if($type == 'renta'){
			$em = $this->getDoctrine()->getManager();
            $user =$em->createQuery('SELECT i FROM CertificadosBundle:DataRenta i WHERE i.nit =:nit GROUP BY i.concepto')
                    ->setParameter('nit', $session->get('nit'))
                    ->getResult();
			foreach($user as $row){
				array_push($data, array('activity' => $row->getConcepto()));
			}
		}
		if($type == 'ica'){
			$em = $this->getDoctrine()->getManager();
                        $user =$em->createQuery('SELECT i FROM CertificadosBundle:DataIca i WHERE i.nit =:nit GROUP BY i.concepto')
                        ->setParameter('nit', $session->get('nit'))
                        ->getResult();
			foreach($user as $row){
                                array_push($data, array('activity' => $row->getConcepto()));
			}
		}
		return new JsonResponse(
                    $data
		);
	}
	
	public function percentsAction(Request $request,$type){
		$session = $request->getSession();
                $auditoria=$this->get('auditoria');
                /*if(!$auditoria->validarSesion($session))
                    return $this->redirect($this->generateUrl('usuario_login'));*/
		$data = array();
		
		if($type == 'iva'){
			$em = $this->getDoctrine()->getManager();
                        $user =$em->createQuery('SELECT i FROM CertificadosBundle:DataIva i WHERE i.nit =:nit GROUP BY i.porcentaje')
                        ->setParameter('nit', $session->get('nit'))
                        ->getResult();
			foreach($user as $row){
				array_push($data, array('percent' => $row->getPorcentaje())
				);
			}
		}
		
		if($type == 'renta'){
			$em = $this->getDoctrine()->getManager();
                        $user =$em->createQuery('SELECT i FROM CertificadosBundle:DataRenta i WHERE i.nit =:nit GROUP BY i.tarifa')
                        ->setParameter('nit', $session->get('nit'))
                        ->getResult();
			foreach($user as $row){
				array_push($data, array('percent' => $row->getTarifa()));
			}
		}
		
		if($type == 'ica'){
			$em = $this->getDoctrine()->getManager();
			$user = $em->createQuery('SELECT i FROM CertificadosBundle:DataIca i WHERE i.nit =:nit GROUP BY i.porcentaje')
            ->setParameter('nit', $session->get('nit'))
            ->getResult();
			foreach($user as $row){
				array_push($data, array('percent' => $row->getPorcentaje())
				);
			}
		}
		return new JsonResponse(
			$data
		);
	}
    public function citiesAction(Request $request,$type){
        $session = $request->getSession();
        $auditoria=$this->get('auditoria');
        /*if(!$auditoria->validarSesion($session))
            return $this->redirect($this->generateUrl('usuario_login'));*/
        $data = array();
        if($type == 'iva'){
            $em = $this->getDoctrine()->getManager();
            $user = $em->createQuery('SELECT i FROM CertificadosBundle:DataIva i WHERE i.nit =:nit GROUP BY i.ciudad')
            ->setParameter('nit', $session->get('nit'))
            ->getResult();
            foreach($user as $row){
                array_push($data, array('city' => $row->getCiudad()));
            }
        }
        if($type == 'ica'){
            $em = $this->getDoctrine()->getManager();
            $user = $em->createQuery('SELECT i FROM CertificadosBundle:DataIca i WHERE i.nit =:nit GROUP BY i.ciudad')
            ->setParameter('nit', $session->get('nit'))
            ->getResult();
            foreach($user as $row){
                array_push($data, array('city' => $row->getCiudad()));
            }
        }
        return new JsonResponse($data);
    }
	
	public function filtersAction(Request $request){

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $certificadoIca=$certificadoIva=$certificadoRenta=false;

        $opcion = $request->get('tipo');

        if ($opcion == 'ica') {

            $certificadoIca= $em->createQuery("SELECT i FROM CertificadosBundle:DataIca i WHERE i.nit =:nit GROUP BY i.ciudad,i.concepto,i.porcentaje,i.fechaPago")
                ->setParameter('nit', $session->get('nit'))
                ->getResult();

        }

        if ($opcion == 'iva') {

            $certificadoIva= $em->createQuery("SELECT i FROM CertificadosBundle:DataIva i WHERE i.nit =:nit GROUP BY i.ciudad,i.porcentaje,i.ano")
                ->setParameter('nit', $session->get('nit'))
                ->getResult();

        }

        if ($opcion == 'renta') {

            $certificadoRenta= $em->createQuery("SELECT i FROM CertificadosBundle:DataRenta i WHERE i.nit =:nit GROUP BY i.year")
                ->setParameter('nit', $session->get('nit'))
                ->getResult();

        } 

        return $this->render('CertificadosBundle:Usuario:filters.html.twig',array('dates' => $this->getYearsFilter(),'ica'=>$certificadoIca,'iva'=>$certificadoIva,'renta'=>$certificadoRenta ));
    }
	
	public function ivareportAction(Request $request,$city, $percent, $year){
            $session = $request->getSession();
            $auditoria=$this->get('auditoria');
            /*if(!$auditoria->validarSesion($session))
                return $this->redirect($this->generateUrl('usuario_login'));*/
            $auditoria->registralog('Consulta Certificado de Iva [ciudad = '.$city.' AND porcentaje = '.$percent.' AND ano = '.$year.']',$session);
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('CertificadosBundle:Usuario')->findOneBy(array('nit'=>$session->get('nit')));
            $date = date('d/m/Y', time());
            $report = $em->createQuery("SELECT i FROM CertificadosBundle:DataIva i WHERE i.nit =:nit AND i.ciudad =:city
                AND i.porcentaje =:percent AND i.ano =:year")
            ->setParameter('nit', $session->get('nit'))
            ->setParameter('city', $city)
            ->setParameter('percent', $percent)
            ->setParameter('year', $year)
            ->getResult();
            return $this->render(
            'CertificadosBundle:Usuario:iva.html.twig',
            array(  'proveedor' => $user,
                    'city' => $city,
                    'current_date' => $auditoria->dateEspanol($date),
                    'report' => $report,
                    'year' => $year
                    ));
	}
	
	public function ivapdfAction(Request $request,$city, $percent, $year){
		$session = $request->getSession();
                $auditoria=$this->get('auditoria');
                /*if(!$auditoria->validarSesion($session))
                    return $this->redirect($this->generateUrl('usuario_login'));*/
                $auditoria->registralog('Genera Certificado de Iva [ciudad = '.$city.' AND porcentaje = '.$percent.' AND ano = '.$year.']',$session);
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('CertificadosBundle:Usuario')->findOneBy(array('nit'=>$session->get('nit')));
		$date = date('d/m/Y', time());
		$query = $em->createQuery(
		    "SELECT i FROM CertificadosBundle:DataIva i WHERE i.nit =:nit AND i.ciudad =:city 
            AND i.porcentaje =:percent AND i.ano =:year ")
        ->setParameter('nit', $session->get('nit'))
        ->setParameter('city', $city)
        ->setParameter('percent', $percent)
        ->setParameter('year', $year)
        ->getResult();		
		
		$pdf = $this->get("my_tcpdfusuario")->create();
                $pdf->SetAutoPageBreak(TRUE);
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Copidrogas');
                $pdf->SetKeywords('impuestos, ica, iva, retenfuente');
                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                // set margins
                $pdf->SetMargins(7, 22, 7);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                $pdf->addPage();
                $pdf->setLineWidth(0.1);
                $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->SetFont('helvetica', 'B', 14);
                $pdf->SetFillColor(2, 56, 127);
                $pdf->SetTextColor(255,255,255);
                $pdf->Cell(110,12, 'CERTIFICADO DE RETENCIÓN DE I.V.A',  0, 0, 'LB',1);
                $pdf->Ln();
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->SetTextColor(0,0,0);
                $pdf->Cell(65,8, 'AÑO GRAVABLE: '.$year,  0, 0, 'L',0);
                $pdf->Cell(65,8, 'DE: 01 ENE '.$year,  0, 0, 'L',0);
                $pdf->Cell(25,8, 'HASTA: 31 DIC '.$year,  0, 0, 'L',0);
                $pdf->Ln();

                $pdf->Cell(25,8, 'FECHA DE EXPEDICIÓN: '.$auditoria->dateEspanol($date),  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->Cell(197,1, '',  'B', 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Nombre o razón social'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, 'Cooperativa Nacional de Droguistas Detallistas'        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'N.I.T'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, '860.026.123-0'        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Dirección'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, 'Autopista Medellin Km 4.7 Vía Siberia Cota'        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Teléfono'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, '437 51 50'        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Ciudad / Municipio'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, 'Cota'        ,  0, 0, 'L',0);
                $pdf->Ln();

                $pdf->Cell(197,1, '',  'B', 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Se practico retención a:'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, $user->getNombre()        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'N.I.T'   ,  0, 0, 'L',0);
                $pdf->Cell(117,6, $user->getNit()        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Dirección'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, $user->getDireccion()        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Teléfono'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, $user->getTel()        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Ciudad / Municipio'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, $user->getCiudad()        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->Cell(197,1, '',  'B', 0, 'L',0);
                $pdf->Ln();

                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->Cell(197,8, 'CONSIGNADO EN LA CIUDAD DE BOGOTÁ SEGÚN EL  SIGUIENTE DETALLE:',  0, 0, 'L',0);

                $pdf->Ln();
                $pdf->SetFont('helvetica', 'B', 7);
                $pdf->SetFillColor(18, 124, 195);
                $pdf->SetTextColor(255,255,255);
                $pdf->Cell(20, 6, 'MES',               'TBLR', 0, 'C',1);
                $pdf->Cell(29, 6, 'VALOR BASE',        'TRB', 0, 'C',1);
                $pdf->Cell(29, 6, 'MONTO IVA',         'TRB', 0, 'C',1);
                $pdf->Cell(29, 6, 'MONTO RETENIDO',    'TRB', 0, 'C',1);
                $pdf->Cell(20, 6, '% RETENIDO',        'TRB', 0, 'C',1);
                $pdf->Cell(22, 6, 'CIUDAD',            'TRB', 0, 'C',1);
                $pdf->Cell(25, 6, '# DECLARACIÓN',     'TRB', 0, 'C',1);
                $pdf->Cell(23, 6, 'FECHA PAGO',        'TRB', 0, 'C',1);
                $pdf->SetTextColor(0,0,0);
                $total_valor_base = 0;
		$total_iva = 0;
		$total_retencion = 0;
                $pdf->SetFont('helvetica', '', 8);
                foreach($query as $row){
                    $mes = $row->getMes();
                    $sumaBase = '$ '.number_format($row->getSumaBase(), 0, ",", ".");
                    $iva = '$ '.number_format($row->getIva(), 0, ",", ".");
                    $reteiva = '$ '.number_format($row->getReteIva(), 0, ",", ".");
                    $porcentaje = $row->getPorcentaje();
                    $ciudad = $row->getCiudad();
                    $declaracion = $row->getDeclaracion();
                    $fecha = $row->getFechaPago()->format('Y-m-d');
                    $total_valor_base = $total_valor_base + $row->getSumaBase();
                    $total_iva = $total_iva + $row->getIva();
                    $total_retencion = $total_retencion + $row->getReteIva();
                    $pdf->Ln();
                    $pdf->Cell(20, 6, $mes,         'TBRL', 0, 'L',0);
                    $pdf->Cell(29, 6, $sumaBase,    'BR', 0, 'R',0);
                    $pdf->Cell(29, 6, $iva,         'BR', 0, 'R',0);
                    $pdf->Cell(29, 6, $reteiva,     'BR', 0, 'C',0);
                    $pdf->Cell(20, 6, $porcentaje,  'BR', 0, 'C',0);
                    $pdf->Cell(22, 6, $ciudad,      'BR', 0, 'C',0);
                    $pdf->Cell(25, 6, $declaracion, 'BR', 0, 'C',0);
                    $pdf->Cell(23, 6, $fecha,       'BR', 0, 'C',0);
                }
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Ln();
                $pdf->SetFillColor(199, 199, 199);
                $pdf->Cell(20, 6, 'TOTAL',                                 'TBRL', 0, 'L',1);
                $pdf->Cell(29, 6, '$ '.number_format($total_valor_base, 0, ",", "."),   'BR', 0, 'R',1);
                $pdf->Cell(29, 6, '$ '.number_format($total_iva, 0, ",", "."),          'BR', 0, 'R',1);
                $pdf->Cell(29, 6, '$ '.number_format($total_retencion, 0, ",", "."),    'BR', 0, 'C',1);
                $pdf->Cell(20, 6, '',                                      'BR', 0, 'C',1);
                $pdf->Cell(22, 6, '',                                      'BR', 0, 'C',1);
                $pdf->Cell(25, 6, '',                                      'BR', 0, 'C',1);
                $pdf->Cell(23, 6, '',                                      'BR', 0, 'C',1);
                $pdf->Ln();
                $pdf->Ln();
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(197,8, 'El Valor total retenido es de: $ '.number_format($total_retencion, 0, ",", "."),  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(197,8, 'OBSERVACIONES: Forma continua impresa por computador no necesita firma autógrafa. (Art. 10 D.R. 836/91)',  0, 0, 'L',0);
                $pdf->Output('IVA-'.$user->getNit().'.pdf','D');
                exit();
	}
	
	public function rentareportAction(Request $request,$city, $percent,$activity, $year){
		$session = $request->getSession();
                $auditoria=$this->get('auditoria');
                /*if(!$auditoria->validarSesion($session))
                    return $this->redirect($this->generateUrl('usuario_login'));*/
                $auditoria->registralog('Consulta Certificado de Renta [year = '.$year.']',$session);
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('CertificadosBundle:Usuario')->findOneBy(array('nit'=>$session->get('nit')));
		$date = date('d/m/Y', time());
		$report = $em->createQuery("SELECT i FROM CertificadosBundle:DataRenta i WHERE i.nit =:nit AND i.year =:year ")
            ->setParameter('nit', $session->get('nit'))
            ->setParameter('year', $year)
            ->getResult();
		return $this->render('CertificadosBundle:Usuario:renta.html.twig',array('proveedor' => $user,'city' => $city,
                                                                                                'current_date' => $auditoria->dateEspanol($date),
                                                                                                'report' => $report,'year' => $year));
	}
	public function rentapdfAction(Request $request,$city, $percent,$activity, $year){
		$session = $request->getSession();
                $auditoria=$this->get('auditoria');
                /*if(!$auditoria->validarSesion($session))
                    return $this->redirect($this->generateUrl('usuario_login'));*/
                $auditoria->registralog('Genera Certificado de Renta [year = '.$year.']',$session);
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('CertificadosBundle:Usuario')->findOneBy(array('nit'=>$session->get('nit')));
		$date = date('d/m/Y', time());
		$query = $em->createQuery("SELECT i FROM CertificadosBundle:DataRenta i WHERE i.nit =:nit AND i.year =:year ")
            ->setParameter('nit', $session->get('nit'))
            ->setParameter('year', $year)
            ->getResult();                      

                $pdf = $this->get("my_tcpdfusuario")->create();
                $pdf->SetAutoPageBreak(TRUE);
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Copidrogas');
                $pdf->SetKeywords('impuestos, ica, iva, retenfuente');
                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                // set margins
                $pdf->SetMargins(7, 22, 7);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                $pdf->addPage();
                $pdf->setLineWidth(0.1);
                $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->SetFont('helvetica', 'B', 14);
                $pdf->SetFillColor(2, 56, 127);
                $pdf->SetTextColor(255,255,255);
                $pdf->Cell(110,12, 'CERTIFICADO DE RETENCIÓN EN LA FUENTE',  0, 0, 'LB',1);
                $pdf->Ln();
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->SetTextColor(0,0,0);
                $pdf->Cell(65,8, 'AÑO GRAVABLE: '.$year,  0, 0, 'L',0);
                $pdf->Cell(65,8, 'DE: 01 ENE '.$year,  0, 0, 'L',0);
                $pdf->Cell(25,8, 'HASTA: 31 DIC '.$year,  0, 0, 'L',0);
                $pdf->Ln();

                $pdf->Cell(25,8, 'FECHA DE EXPEDICIÓN: '.$auditoria->dateEspanol($date),  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->Cell(197,1, '',  'B', 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Nombre o razón social'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, 'Cooperativa Nacional de Droguistas Detallistas'        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'N.I.T'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, '860.026.123-0'        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Dirección'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, 'Autopista Medellin Km 4.7 Vía Siberia Cota'        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Teléfono'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, '437 51 50'        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Ciudad / Municipio'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, 'Cota'        ,  0, 0, 'L',0);
                $pdf->Ln();

                $pdf->Cell(197,1, '',  'B', 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Se practico retención a:'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, $user->getNombre()        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'N.I.T'   ,  0, 0, 'L',0);
                $pdf->Cell(117,6, $user->getNit()        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Dirección'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, $user->getDireccion()        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Teléfono'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, $user->getTel()        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(80, 6, 'Ciudad / Municipio'   ,  0, 0, 'L',0);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(117,6, $user->getCiudad()        ,  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->Cell(197,1, '',  'B', 0, 'L',0);
                $pdf->Ln();

                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(197,8, 'Consignado en la Respectiva Jurisdicción según el siguiente detalle',  0, 0, 'L',0);

                $pdf->Ln();
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetFillColor(18, 124, 195);
                $pdf->SetTextColor(255,255,255);
                $pdf->Cell(77, 6, 'CONCEPTO',              'TBLR', 0, 'C',1);
                $pdf->Cell(30, 6, 'TARIFA',   'TRB', 0, 'C',1);
                $pdf->Cell(45, 6, 'BASE',   'TRB', 0, 'C',1);
                $pdf->Cell(45, 6, 'RETENCIÒN',       'TRB', 0, 'C',1);
                $pdf->SetTextColor(0,0,0);
                $total_valor_base = 0;
		$total_iva = 0;
		$total_retencion = 0;
                $pdf->SetFont('helvetica', '', 10);
                foreach($query as $row){
                    $porcentaje = $row->getConcepto();
                    $tarifa = $row->getTarifa();
                    $sumaBase = '$ '.number_format($row->getBase(), 0, ",", ".");
                    $rete = '$ '.number_format($row->getRetencion(), 0, ",", ".");


                    $total_valor_base = $total_valor_base + $row->getBase();
                    $total_retencion = $total_retencion + $row->getRetencion();

                    $pdf->Ln();
                    $pdf->Cell(77, 6, $porcentaje,         'TBRL', 0, 'L',0);
                    $pdf->Cell(30, 6, $tarifa,    'BR', 0, 'C',0);
                    $pdf->Cell(45, 6, $sumaBase,        'BR', 0, 'R',0);
                    $pdf->Cell(45, 6, $rete,  'BR', 0, 'R',0);
                }
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Ln();
                $pdf->SetFillColor(199, 199, 199);
                $pdf->Cell(77, 6, 'TOTAL',                                 'TBRL', 0, 'L',1);
                $pdf->Cell(30, 6, '',   'BR', 0, 'R',1);
                $pdf->Cell(45, 6, '$ '.number_format($total_valor_base),         'BR', 0, 'R',1);
                $pdf->Cell(45, 6, '$ '.number_format($total_retencion),                                      'BR', 0, 'R',1);
                $pdf->Ln();
                $pdf->Ln();
                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->Cell(197,8, 'Estos valores fueron consignados oportunamente en la Dirección de Impuestos y Aduanas Nacionales DIAN de Bogotá.',  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 7);
                $pdf->Cell(197,5, 'Se expide este certificado en Cumplimiento de lo establecido en el Articulo 381 del Estatuto tributario, el Decreto 380/86',  0, 0, 'L',0);
                $pdf->Ln();
                $pdf->Cell(197,5, 'De acuerdo con el artículo 10 del Decreto 836 de 1991, este certificado no requiere firma autógrafa',  0, 0, 'L',0);
                $pdf->Output('RETENCION-'.$user->getNit().'.pdf','D');
                exit();
	}

	public function icareportAction(Request $request, $city, $percent,$activity, $year){
		$session = $request->getSession();
                $auditoria=$this->get('auditoria');
                /*if(!$auditoria->validarSesion($session))
                    return $this->redirect($this->generateUrl('usuario_login'));*/
                $auditoria->registralog('Consulta Certificado de Ica [concepto = '.$activity.'  AND porcentaje = '.$percent.' AND ciudad = '.$auditoria->remplazartildes($city).' AND fechaPago = '.$year.']',$session);
		$em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('CertificadosBundle:Usuario')->findOneBy(array('nit'=>$session->get('nit')));
		$date = date('d/m/Y', time());
		$report = $em->createQuery("SELECT i FROM CertificadosBundle:DataIca i WHERE i.nit =:nit AND i.concepto =:concepto 
                                            AND i.porcentaje =:percent
                                            AND i.ciudad =:city
                                            AND i.fechaPago =:year")
                ->setParameter('nit', $session->get('nit'))
                ->setParameter('concepto', $activity)
                ->setParameter('percent', $percent)
                ->setParameter('city', $auditoria->remplazartildes($city))
                ->setParameter('year', $year)
                ->getResult();
                
		return $this->render('CertificadosBundle:Usuario:ica.html.twig',array('proveedor' => $user,'city' => $city,
                                                                                                'current_date' => $auditoria->dateEspanol($date),
                                                                                                'report' => $report,'year' => $year));
	}

    public function icapdfAction(Request $request, $city, $percent,$activity, $year){
    	$session = $request->getSession();

        $auditoria=$this->get('auditoria');
        $auditoria->registralog('Genera Certificado de Ica [concepto = '.$activity.'  AND porcentaje = '.$percent.' AND ciudad = '.$auditoria->remplazartildes($city).' AND fechaPago = '.$year.']',$session);
    	$em = $this->getDoctrine()->getManager();
    	$user = $em->getRepository('CertificadosBundle:Usuario')->findOneBy(array('nit'=> $session->get('nit')));
    	$date = date('d/m/Y', time());
    	$query = $em->createQuery(
    	    "SELECT i FROM CertificadosBundle:DataIca i WHERE i.nit =:nit AND i.concepto =:concepto 
    	    AND i.porcentaje =:percent
    	    AND i.ciudad =:city
    		AND i.fechaPago =:year")
            ->setParameter('nit', $session->get('nit'))
            ->setParameter('concepto', $activity)
            ->setParameter('percent', $percent)
            ->setParameter('city', $auditoria->remplazartildes($city))
            ->setParameter('year', $year)
            ->getResult();
        $pdf = $this->get("my_tcpdfusuario")->create();
        $pdf->SetAutoPageBreak(TRUE);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Copidrogas');
        $pdf->SetKeywords('impuestos, ica, iva, retenfuente');
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $pdf->SetMargins(7, 22, 7);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->addPage();
        $pdf->setLineWidth(0.1);
        $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetFillColor(2, 56, 127);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(110,12, 'CERTIFICADO DE RETENCIÓN DE I.C.A',  0, 0, 'LB',1);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(65,8, 'AÑO GRAVABLE: '.$year,  0, 0, 'L',0);
        $pdf->Cell(65,8, 'DE: 01 ENE '.$year,  0, 0, 'L',0);
        $pdf->Cell(25,8, 'HASTA: 31 DIC '.$year,  0, 0, 'L',0);
        $pdf->Ln();

        $pdf->Cell(25,8, 'FECHA DE EXPEDICIÓN: '.$auditoria->dateEspanol($date),  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->Cell(197,1, '',  'B', 0, 'L',0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, 'Nombre o razón social'   ,  0, 0, 'L',0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(117,6, 'Cooperativa Nacional de Droguistas Detallistas'        ,  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, 'N.I.T'   ,  0, 0, 'L',0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(117,6, '860.026.123-0'        ,  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, 'Dirección'   ,  0, 0, 'L',0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(117,6, 'Autopista Medellin Km 4.7 Vía Siberia Cota'        ,  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, 'Teléfono'   ,  0, 0, 'L',0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(117,6, '437 51 50'        ,  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, 'Ciudad / Municipio'   ,  0, 0, 'L',0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(117,6, 'Cota'        ,  0, 0, 'L',0);
        $pdf->Ln();

        $pdf->Cell(197,1, '',  'B', 0, 'L',0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, 'Se practico retención a:'   ,  0, 0, 'L',0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(117,6, $user->getNombre(),  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, 'N.I.T'   ,  0, 0, 'L',0);
        $pdf->Cell(117,6, $user->getNit(),  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, 'Dirección'   ,  0, 0, 'L',0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(117,6, $user->getDireccion(),  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, 'Teléfono'   ,  0, 0, 'L',0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(117,6, $user->getTel(),  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, 'Ciudad / Municipio'   ,  0, 0, 'L',0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(117,6, $user->getCiudad(),  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->Cell(197,1, '',  'B', 0, 'L',0);
        $pdf->Ln();

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(197,8, 'Consignado en la Respectiva Jurisdicción según el siguiente detalle',  0, 0, 'L',0);

        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(18, 124, 195);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(30, 6, 'MES',              'TBLR', 0, 'C',1);
        $pdf->Cell(48, 6, 'BASE RETENCIÓN',   'TRB', 0, 'C',1);
        $pdf->Cell(48, 6, 'MONTO RETENIDO',   'TRB', 0, 'C',1);
        $pdf->Cell(28, 6, '% RETENIDO',       'TRB', 0, 'C',1);
        $pdf->Cell(43, 6, 'CIUDAD',           'TRB', 0, 'C',1);
        $pdf->SetTextColor(0,0,0);
        $total_valor_base = 0;
        $total_rete = 0;
        $pdf->SetFont('helvetica', '', 10);
        foreach($query as $row){
            $mes = $row->getMes();
            $sumaBase = '$ '.number_format($row->getSumaBase());
            $rete = '$ '.number_format($row->getValorRetenido());
            $porcentaje = $row->getPorcentaje();
            $ciudad = $row->getCiudad();

            $pdf->Ln();
            $pdf->Cell(30, 6, $mes,         'TBRL', 0, 'L',0);
            $pdf->Cell(48, 6, $sumaBase,    'BR', 0, 'R',0);
            $pdf->Cell(48, 6, $rete,        'BR', 0, 'R',0);
            $pdf->Cell(28, 6, $porcentaje,  'BR', 0, 'C',0);
            $pdf->Cell(43, 6, $ciudad,      'BR', 0, 'C',0);
            $total_valor_base = $total_valor_base + $row->getSumaBase(); 
            $total_rete = $total_rete + $row->getValorRetenido(); 
        }
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Ln();
        $pdf->SetFillColor(199, 199, 199);
        $pdf->Cell(30, 6, 'TOTAL',                                 'TBRL', 0, 'L',1);
        $pdf->Cell(48, 6, '$ '.number_format($total_valor_base),   'BR', 0, 'R',1);
        $pdf->Cell(48, 6, '$ '.number_format($total_rete),         'BR', 0, 'R',1);
        $pdf->Cell(28, 6, '',                                      'BR', 0, 'C',1);
        $pdf->Cell(43, 6, '',                                      'BR', 0, 'C',1);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(197,8, 'El Valor total retenido es de: '.'$ '.number_format($total_rete),  0, 0, 'L',0);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(197,8, 'OBSERVACIONES: Forma continua impresa por computador no necesita firma autógrafa. (Art. 10 D.R. 836/91)',  0, 0, 'L',0);
        $pdf->Output('ICA-'.$user->getNit().'.pdf','D');
        exit();
		
	}
}
