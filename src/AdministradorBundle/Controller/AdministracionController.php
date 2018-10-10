<?php
namespace AdministradorBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AdministradorBundle\Entity\Usuarios;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AdministradorBundle\Entity\DataIva;
use AdministradorBundle\Entity\DataIca;
use AdministradorBundle\Entity\DataRenta;
use AdministradorBundle\Entity\Admin;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class AdministracionController extends Controller
{
    public function uploadfileAction(Request $request){ 
        foreach($request->files as $uploadedFile) {
            $file = $uploadedFile[0]->move('uploads', $uploadedFile[0]->getClientOriginalName());
            $data = array('file_url' => $uploadedFile[0]->getClientOriginalName());
        }
        return new JsonResponse($data);
    }
    public function loadivaAction(Request $request){

        $session = $request->getSession();
        $form = $this->createFormBuilder()
                                    ->add('hoja1', FileType::class,
                                        array('label' => 'Seleccione la Hoja3:'
                                    ))
                                    ->add('fechaInicioIva', TextType::class,
                                        array('label' => 'fecha inicio'))
                                    ->add('fechaFinalIva', TextType::class,
                                        array('label' => 'fecha final'))
                                    ->getForm();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);
            if ($form->isValid()) {

                $fechaFinal="";

                if ($request->get('form')['fechaFinalIva']) {

                    $fecha = $request->get('form')['fechaFinalIva']."-01";
                    $timestamp = strtotime( $fecha );
                    $diasdelmes = date( "t", $timestamp );
                    $fechaFinal=$request->get('form')['fechaFinalIva'].'-'.$diasdelmes;

                } else {

                    $fecha = $request->get('form')['fechaInicioIva']."-01";
                    $timestamp = strtotime( $fecha );
                    $diasdelmes = date( "t", $timestamp );
                    $fechaFinal=$request->get('form')['fechaInicioIva'].'-'. $diasdelmes;

                }

                set_time_limit(5000);
                ini_set('memory_limit', '512M');

                $em = $this->getDoctrine()->getManager();
                $con = $em->getConnection();
                $files = $request->files->get($form->getName());
                $file = $files['hoja1'];
                $pathUpload='./uploads/';
                $file->move('./uploads/',$file->getClientOriginalName());
                $filename = $pathUpload.$file->getClientOriginalName();
                $SQL1='INSERT INTO data_iva (nit, nombre, direccion, ciudad, mes, declaracion, fecha_pago, porcentaje, suma_base, iva, rete_iva, ano) VALUES ';
                $sql="";
                $NumeroRegistros=$contador=0;
                $fp = fopen ($filename,"r");
                $date = date($request->get('form')['fechaInicioIva']);
                $contador=$NumeroRegistros=0;
                $control=0;

                while($data = fgetcsv ($fp, 1000, ";"))
                {
                    if ($data[11]!='') {

                        if ($control==0) {

                            $control=1;
                            $BorrarActuales = 'DELETE FROM data_iva WHERE fecha_pago >= "'.$request->get('form')['fechaInicioIva'].'-01" AND fecha_pago <= "'.$fechaFinal.'"';
                            $con->query($BorrarActuales)->execute();

                        }//end if

                        if ($contador > 0)
                            $sql.=',';

                        $temp = (float)(str_replace(',', '.', $data[7]));    
                        $sql .= ' ("'.$data[0].'", "'.sanear_string(utf8_encode($data[1])).'", "'.sanear_string(utf8_encode($data[2])).'", "'.sanear_string(utf8_encode($data[4])).'", "'.sanear_string(utf8_encode($data[3])).'", "'.$data[5].'", "'.$data[6].'", "'. $temp.'", "'.floatval($data[8]).'", "'.floatval($data[9]).'", "'.floatval($data[10]).'", "'.floatval($data[11]).'")';

                        $contador++;
                        $NumeroRegistros++;

                        if ($contador == 1) {

                            $con->query(stripslashes($SQL1.$sql));
                            $sql="";
                            $contador=0;

                        }//end if

                        $NumeroRegistros++;

                    }//endif

                }//end while

                if ($contador > 0) 
                    $con->query($SQL1.$sql.";");$sql="";
                
                $resp['registros'] = $NumeroRegistros;
                $response->setContent(json_encode($resp));

                return $response;

            } else {

                throw $this->createNotFoundException('El formulario no es valido1.');

            }

        }  else {

            throw $this->createNotFoundException('Metodo no valido1.');

        }

        exit();

    }//end action

    public function loadicaAction(Request $request){
    $form = $this->createFormBuilder()
                                ->add('hoja2', FileType::class,array('label' => 'Seleccione la Hoja2:'))
                                ->add('fechaInicioIca', TextType::class,array('label' => 'fecha inicio'))
                                ->add('fechaFinalIca', TextType::class,array('label' => 'fecha final'))
                                ->getForm();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $mesNumero=array('ENERO'=>'01','FEBRERO'=>'02','MARZO'=>'03','ABRIL'=>'04','MAYO'=>'05','JUNIO'=>'06','JULIO'=>'07','AGOSTO'=>'08','SEPTIEMBRE'=>'09','OCTUBRE'=>'10','NOVIEMBRE'=>'11','DICIEMBRE'=>'12');
                    $fechaFinal="";
                    if ($request->get('form')['fechaFinalIca']) {
                        $fecha = $request->get('form')['fechaFinalIca']."-01";
                        $timestamp = strtotime( $fecha );
                        $diasdelmes = date( "t", $timestamp );
                        $fechaFinal=$request->get('form')['fechaFinalIca']."-".$diasdelmes;
                    }else{
                        $fecha = $request->get('form')['fechaInicioIca']."-01";
                        $timestamp = strtotime( $fecha );
                        $diasdelmes = date( "t", $timestamp );
                        $fechaFinal=$request->get('form')['fechaInicioIca'].'-'.$diasdelmes;
                    }
                    set_time_limit(5000);
                    ini_set('memory_limit', '512M');
                    $em = $this->getDoctrine()->getManager();
                    $con = $em->getConnection();
                    $files = $request->files->get($form->getName());
                    $file = $files['hoja2'];
                    $pathUpload='./uploads/';
                    $file->move($pathUpload,$file->getClientOriginalName());
                    $filename = $pathUpload.$file->getClientOriginalName();
                    $SQL1='INSERT INTO data_ica (nit, nombre, direccion, concepto, ciudad, mes, declaracion, fecha_pago, porcentaje, suma_base, valor_retenido,fecha_para_eliminacion) VALUES ';
                    $sql="";$NumeroRegistros=$contador=0;
                    $fp = fopen ($filename,"r");
                    $contador=$NumeroRegistros=0;
                    $date = date($request->get('form')['fechaInicioIca']);
                    $control=0;
                    while($data = fgetcsv ($fp, 1000, ";"))
                    {
                        if($data[9]!=''){
                            if($control==0){
                                $control=1;
                                $BorrarActuales='DELETE FROM data_ica WHERE fecha_para_eliminacion >= "'.$request->get('form')['fechaInicioIca'].'-01" AND fecha_para_eliminacion < "'.$fechaFinal.'"';
                                $con->query($BorrarActuales)->execute();
                            }
                            if($contador > 0)
                                $sql.=",";
                            $sql .= " ('".$data[0]."', '". htmlentities($data[1])."', '".htmlentities($data[2])."', '".htmlentities($data[5])."', '".htmlentities(trim($data[3]))."', '".trim($data[4])."', '0', '".$data[9]."', '".$data[6]."', '".$data[7]."', '".$data[8]."', '".$data[9]."-".$mesNumero[strtoupper(trim($data[4]))]."-01')";
                            //echo $SQL1.$sql;exit();
                            $contador++;
                            $NumeroRegistros++;
                            if($contador==200){
                                $con->query($SQL1.$sql);$sql="";
                                $contador=0;
                            }
                            $NumeroRegistros++;
                        }
                    }
                    if($contador>0){
                        $con->query($SQL1.$sql.";");$sql="";
                    }
                    $resp['registros'] = $NumeroRegistros;
                    $response->setContent(json_encode($resp));
                    return $response;
                }else{
                    throw $this->createNotFoundException('El formulario no es valido1.');
                }
            }  else {
                throw $this->createNotFoundException('Metodo no valido1.');
            }
            exit();
    }

    public function loadrentaAction(Request $request){
        $form = $this->createFormBuilder()
                                    ->add('hoja3', FileType::class,array('label' => 'Seleccione la Hoja3:'))
                                    ->getForm();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    set_time_limit(5000);
                    ini_set('memory_limit', '512M');
                    $em = $this->getDoctrine()->getManager();
                    $con = $em->getConnection();
                    $files = $request->files->get($form->getName());
                    $file = $files['hoja3'];
                    $pathUpload='./uploads/';
                    $file->move($pathUpload,$file->getClientOriginalName());
                    $filename = $pathUpload.$file->getClientOriginalName();
                    $SQL1='INSERT INTO data_renta (nit, nombre, direccion, concepto, tarifa, base, retencion, year) VALUES ';
                    $sql="";$NumeroRegistros=$contador=0;
                    $fp = fopen ($filename,"r");
                    $contador=$NumeroRegistros=0;
                    $control=0;
                    while($data = fgetcsv ($fp, 1000, ";"))
                    {
                        if($data[7]!=''){
                            if($control==0){
                                $control=1;
                                $BorrarActuales='DELETE FROM data_renta WHERE year = "'.$data[7].'"';
                                $con->query($BorrarActuales)->execute();
                            }
                            if($contador > 0)
                                $sql.=",";
                            $sql .= " ('".$data[0]."', '". htmlentities($data[1])."', '".htmlentities($data[2])."', '".htmlentities($data[3])."', '".$data[4]."', '".$data[5]."', '".$data[6]."', '".$data[7]."')";
                            $contador++;
                            $NumeroRegistros++;
                            if($contador==200){
                                $con->query($SQL1.$sql);$sql="";
                                $contador=0;
                            }
                            $NumeroRegistros++;
                        }
                    }
                    if($contador>0){
                        $con->query($SQL1.$sql.";");$sql="";
                    }
                    $resp['registros'] = $NumeroRegistros;
                    $response->setContent(json_encode($resp));
                    return $response;
                }else{
                    throw $this->createNotFoundException('El formulario no es valido1.');
                }
            }  else {
                throw $this->createNotFoundException('Metodo no valido1.');
            }
            exit();
    }
    public function loaddataAction(Request $request)
    {
        $session = $request->getSession();
        $auditoria=$this->get('auditoria');
        /*if(!$auditoria->validarSesion($session))
            return $this->redirect($this->generateUrl('admin_login'));*/
        $form1 = $this->createFormBuilder()
            ->add('fechaInicioIva', TextType::class,
                 array('label' => 'Fecha Inicio','required'=>true,'attr' => array('class' => 'form-control form-control-sm')))
            ->add('fechaFinalIva', TextType::class,
                 array('label' => 'Fecha Final','required'=>false,'attr' => array('class' => 'form-control form-control-sm')))
            ->add('hoja1', FileType::class,
                 array('label' => 'IVA'))
            ->getForm();
        //$form2 = $this->createForm(new Hoja2Type());
        $form2 = $this->createFormBuilder()
            ->add('fechaInicioIca', TextType::class,
                 array('label' => 'Fecha Inicio','required'=>true,'attr' => array('class' => 'form-control form-control-sm')))
            ->add('fechaFinalIca', TextType::class,
                 array('label' => 'Fecha Final','required'=>false,'attr' => array('class' => 'form-control form-control-sm')))
            ->add('hoja2', FileType::class,
                 array('label' => 'ICA'))
            ->getForm();
        $form3 = $this->createFormBuilder()
            ->add('hoja3', FileType::class,
                 array('label' => 'RENTA'))
            ->getForm();

        return $this->render('AdministradorBundle:Administracion:actualizar.html.twig',array('activo'=>'data','form1' =>$form1->createView(),
            'form2'         =>$form2->createView(),
            'form3'         =>$form3->createView()));
    }

    public function downloadPlantillaIVAAction(){

            
            $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
            $objPHPExcel->setActiveSheetIndex(0);

            $objPHPExcel->getActiveSheet()->setTitle('plantIva');
            // Set properties
            $objPHPExcel->getProperties()->setTitle("Plantilla cargar Iva");
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'NIT');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'NOMBRE');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'DIRECCION');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'CIUDAD');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'MES');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'DECLARACION');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'FECHA PAGO');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'PORCENTAJE');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'SUMA BASE');
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'IVA');
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'RETE IVA');
            $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'AÑO');
            
           $objPHPExcel->setActiveSheetIndex(0);
            //$objPHPExcel->getActiveSheet()->setTitle('Partidos');
            header('Content-Type: ');
            header('Content-Disposition: attachment;filename="Plantilla_Datos_Iva.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
            $objWriter->save('php://output'); 
            exit();   
    }
}
    function sanear_string($string)
    {
     
        $string = trim($string);
     
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('&aacute;', 'a', 'a', 'a', 'a', '&Aacute;', 'A', 'A', 'A'),
            $string
        );
     
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('&eacute;', 'e', 'e', 'e', '&Eacute;', 'E', 'E', 'E'),
            $string
        );
     
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('&iacute;', 'i', 'i', 'i', '&Iacute;', 'I', 'I', 'I'),
            $string
        );
     
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('&oacute;', 'o', 'o', 'o', '&Oacute;', 'O', 'O', 'O'),
            $string
        );
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('&uacute;', 'u', 'u', 'u', '&Uacute;', 'U', 'U', 'U'),
            $string
        );
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('&ntilde;', '&Ntilde;', 'c', 'C',),
            $string
        );
        //Esta parte se encarga de eliminar cualquier caracter extraño
        /*$string = str_replace(
            array("\", "¨", "º", "-", "~",
                 "#", "@", "|", "!", '"',
                 "·", "$", "%", "&", "/",
                 "(", ")", "?", "'", "¡",
                 "¿", "[", "^", "<code>", "]",
                 "+", "}", "{", "¨", "´",
                 ">", "< ", ";", ",", ":",
                 ".", " "),
            '',
            $string
        );*/
        return htmlentities($string);
    }