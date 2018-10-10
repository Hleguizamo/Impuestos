<?php

namespace AdministradorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataIva
 *
 * @ORM\Table(name="data_iva", indexes={@ORM\Index(name="nit", columns={"nit"})})
 * @ORM\Entity
 */
class DataIva
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
     * @ORM\Column(name="nit", type="string", length=455, nullable=false)
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
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="ciudad", type="string", length=45, nullable=false)
     */
    private $ciudad;

    /**
     * @var string
     *
     * @ORM\Column(name="mes", type="string", length=45, nullable=false)
     */
    private $mes;

    /**
     * @var string
     *
     * @ORM\Column(name="declaracion", type="string", length=20, nullable=false)
     */
    private $declaracion = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_pago", type="date", nullable=false)
     */
    private $fechaPago;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje", type="string", length=10, nullable=false)
     */
    private $porcentaje = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="suma_base", type="bigint", nullable=false)
     */
    private $sumaBase;

    /**
     * @var integer
     *
     * @ORM\Column(name="iva", type="integer", nullable=false)
     */
    private $iva;

    /**
     * @var integer
     *
     * @ORM\Column(name="rete_iva", type="integer", nullable=true)
     */
    private $reteIva;

    /**
     * @var string
     *
     * @ORM\Column(name="ano", type="string", length=10, nullable=true)
     */
    private $ano;



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
     * @return DataIva
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
     * @return DataIva
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
     * @return DataIva
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
     * Set ciudad
     *
     * @param string $ciudad
     *
     * @return DataIva
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
     * @return DataIva
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
     * @param string $declaracion
     *
     * @return DataIva
     */
    public function setDeclaracion($declaracion)
    {
        $this->declaracion = $declaracion;

        return $this;
    }

    /**
     * Get declaracion
     *
     * @return string
     */
    public function getDeclaracion()
    {
        return $this->declaracion;
    }

    /**
     * Set fechaPago
     *
     * @param \DateTime $fechaPago
     *
     * @return DataIva
     */
    public function setFechaPago($fechaPago)
    {
        $this->fechaPago = $fechaPago;

        return $this;
    }

    /**
     * Get fechaPago
     *
     * @return \DateTime
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
     * @return DataIva
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
     * @param integer $sumaBase
     *
     * @return DataIva
     */
    public function setSumaBase($sumaBase)
    {
        $this->sumaBase = $sumaBase;

        return $this;
    }

    /**
     * Get sumaBase
     *
     * @return integer
     */
    public function getSumaBase()
    {
        return $this->sumaBase;
    }

    /**
     * Set iva
     *
     * @param integer $iva
     *
     * @return DataIva
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva
     *
     * @return integer
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set reteIva
     *
     * @param integer $reteIva
     *
     * @return DataIva
     */
    public function setReteIva($reteIva)
    {
        $this->reteIva = $reteIva;

        return $this;
    }

    /**
     * Get reteIva
     *
     * @return integer
     */
    public function getReteIva()
    {
        return $this->reteIva;
    }

    /**
     * Set ano
     *
     * @param string $ano
     *
     * @return DataIva
     */
    public function setAno($ano)
    {
        $this->ano = $ano;

        return $this;
    }

    /**
     * Get ano
     *
     * @return string
     */
    public function getAno()
    {
        return $this->ano;
    }
}
