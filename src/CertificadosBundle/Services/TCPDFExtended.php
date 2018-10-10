<?php

namespace CertificadosBundle\Services;

class TCPDFExtended extends \TCPDF  {

	public function create(){
		return new self();
	}
        //Page header
    public function Header() {
        //$this->Cell(0, 15, '', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        /*$this->SetFillColor(255, 126, 26);
        $this->Cell(197,14, '',  0, 0, 'C',1);*/
        $this->Image('images/logoCopidrogas.png',$this->GetY()+2, $this->GetY(),'','','','','',false,300,'',false,false,0,false,false,false,false,false);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
    
}
