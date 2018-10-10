<?php

namespace CertificadosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataIca
 *
 * @ORM\Table(name="data_ica", indexes={@ORM\Index(name="nit", columns={"nit"})})
 * @ORM\Entity
 */
class DataIca
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nit", type="string", length=20, nullable=false)
     */
    private $nit;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=455, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=455, nullable=false)
     */
    private $direccion = '';

    /**
     * @var string
     *
     * @ORM\Column(name="concepto", type="string", length=455, nullable=false)
     */
    private $concepto;

    /**
     * @var string
     *
     * @ORM\Column(name="ciudad", type="string", length=455, nullable=false)
     */
    private $ciudad;

    /**
     * @var string
     *
     * @ORM\Column(name="mes", type="string", length=45, nullable=false)
     */
    private $mes;

    /**
     * @var integer
     *
     * @ORM\Column(name="declaracion", type="integer", nullable=false)
     */
    private $declaracion;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha_pago", type="string", length=20, nullable=true)
     */
    private $fechaPago;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje", type="string", length=50, nullable=false)
     */
    private $porcentaje;

    /**
     * @var string
     *
     * @ORM\Column(name="suma_base", type="string", length=50, nullable=false)
     */
    private $sumaBase;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_retenido", type="string", length=50, nullable=false)
     */
    private $valorRetenido;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_para_eliminacion", type="date", nullable=true)
     */
    private $fechaParaEliminacion;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nit
     *
     * @param string $nit
     *
     * @return DataIca
     */
    public function setNit($nit)
    {
        $this->nit = $nit;

        return $this;
    }

    /**
     * Get nit
     *
     * @return string
     */
    public function getNit()
    {
        return $this->nit;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return DataIca
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return DataIca
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set concepto
     *
     * @param string $concepto
     *
     * @return DataIca
     */
    public function setConcepto($concepto)
    {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return string
     */
    public function getConcepto()
    {
        return $this->concepto;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     *
     * @return DataIca
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get ciudad
     *
     * @return string
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set mes
     *
     * @param string $mes
     *
     * @return DataIca
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return string
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set declaracion
     *
     * @param integer $declaracion
     *
     * @return DataIca
     */
    public function setDeclaracion($declaracion)
    {
        $this->declaracion = $declaracion;

        return $this;
    }

    /**
     * Get declaracion
     *
     * @return integer
     */
    public function getDeclaracion()
    {
        return $this->declaracion;
    }

    /**
     * Set fechaPago
     *
     * @param string $fechaPago
     *
     * @return DataIca
     */
    public function setFechaPago($fechaPago)
    {
        $this->fechaPago = $fechaPago;

        return $this;
    }

    /**
     * Get fechaPago
     *
     * @return string
     */
    public function getFechaPago()
    {
        return $this->fechaPago;
    }

    /**
     * Set porcentaje
     *
     * @param string $porcentaje
     *
     * @return DataIca
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje
     *
     * @return string
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set sumaBase
     *
     * @param string $sumaBase
     *
     * @return DataIca
     */
    public function setSumaBase($sumaBase)
    {
        $this->sumaBase = $sumaBase;

        return $this;
    }

    /**
     * Get sumaBase
     *
     * @return string
     */
    public function getSumaBase()
    {
        return $this->sumaBase;
    }

    /**
     * Set valorRetenido
     *
     * @param string $valorRetenido
     *
     * @return DataIca
     */
    public function setValorRetenido($valorRetenido)
    {
        $this->valorRetenido = $valorRetenido;

        return $this;
    }

    /**
     * Get valorRetenido
     *
     * @return string
     */
    public function getValorRetenido()
    {
        return $this->valorRetenido;
    }

    /**
     * Set fechaParaEliminacion
     *
     * @param \DateTime $fechaParaEliminacion
     *
     * @return DataIca
     */
    public function setFechaParaEliminacion($fechaParaEliminacion)
    {
        $this->fechaParaEliminacion = $fechaParaEliminacion;

        return $this;
    }

    /**
     * Get fechaParaEliminacion
     *
     * @return \DateTime
     */
    public function getFechaParaEliminacion()
    {
        return $this->fechaParaEliminacion;
    }
}
