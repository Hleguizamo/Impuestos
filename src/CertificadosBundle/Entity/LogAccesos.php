<?php

namespace CertificadosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogAccesos
 *
 * @ORM\Table(name="log_accesos", indexes={@ORM\Index(name="usuario_id", columns={"usuario"})})
 * @ORM\Entity
 */
class LogAccesos
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=250, nullable=false)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="nit", type="string", length=20, nullable=false)
     */
    private $nit;

    /**
     * @var string
     *
     * @ORM\Column(name="actividad", type="string", length=250, nullable=false)
     */
    private $actividad;



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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return LogAccesos
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     *
     * @return LogAccesos
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set nit
     *
     * @param string $nit
     *
     * @return LogAccesos
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
     * Set actividad
     *
     * @param string $actividad
     *
     * @return LogAccesos
     */
    public function setActividad($actividad)
    {
        $this->actividad = $actividad;

        return $this;
    }

    /**
     * Get actividad
     *
     * @return string
     */
    public function getActividad()
    {
        return $this->actividad;
    }
}
