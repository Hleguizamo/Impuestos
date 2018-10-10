<?php

namespace AdministradorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataRenta
 *
 * @ORM\Table(name="data_renta", indexes={@ORM\Index(name="nit", columns={"nit"})})
 * @ORM\Entity
 */
class DataRenta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="nit", type="bigint", nullable=false)
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
     * @ORM\Column(name="concepto", type="string", length=250, nullable=false)
     */
    private $concepto = '';

    /**
     * @var string
     *
     * @ORM\Column(name="tarifa", type="string", length=50, nullable=false)
     */
    private $tarifa;

    /**
     * @var string
     *
     * @ORM\Column(name="base", type="string", length=50, nullable=false)
     */
    private $base;

    /**
     * @var string
     *
     * @ORM\Column(name="retencion", type="string", length=50, nullable=false)
     */
    private $retencion;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=10, nullable=true)
     */
    private $year;



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
     * @param integer $nit
     *
     * @return DataRenta
     */
    public function setNit($nit)
    {
        $this->nit = $nit;

        return $this;
    }

    /**
     * Get nit
     *
     * @return integer
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
     * @return DataRenta
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
     * @return DataRenta
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
     * @return DataRenta
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
     * Set tarifa
     *
     * @param string $tarifa
     *
     * @return DataRenta
     */
    public function setTarifa($tarifa)
    {
        $this->tarifa = $tarifa;

        return $this;
    }

    /**
     * Get tarifa
     *
     * @return string
     */
    public function getTarifa()
    {
        return $this->tarifa;
    }

    /**
     * Set base
     *
     * @param string $base
     *
     * @return DataRenta
     */
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Get base
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set retencion
     *
     * @param string $retencion
     *
     * @return DataRenta
     */
    public function setRetencion($retencion)
    {
        $this->retencion = $retencion;

        return $this;
    }

    /**
     * Get retencion
     *
     * @return string
     */
    public function getRetencion()
    {
        return $this->retencion;
    }

    /**
     * Set year
     *
     * @param string $year
     *
     * @return DataRenta
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }
}
