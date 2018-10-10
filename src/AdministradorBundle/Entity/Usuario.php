<?php

namespace AdministradorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario", uniqueConstraints={@ORM\UniqueConstraint(name="nit_2", columns={"nit"})}, indexes={@ORM\Index(name="nit", columns={"nit"})})
 * @ORM\Entity
 */
class Usuario
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
    private $nit = '';

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=250, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=250, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=20, nullable=true)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="representante", type="string", length=500, nullable=true)
     */
    private $representante;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=250, nullable=true)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=15, nullable=true)
     */
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="enabled", type="integer", nullable=true)
     */
    private $enabled = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="ciudad", type="string", length=70, nullable=true)
     */
    private $ciudad;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cambio_clave", type="datetime", nullable=true)
     */
    private $cambioClave;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_cambio", type="string", length=250, nullable=true)
     */
    private $nombreCambio;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion_cambio", type="string", length=250, nullable=true)
     */
    private $direccionCambio;

    /**
     * @var string
     *
     * @ORM\Column(name="tel_cambio", type="string", length=20, nullable=true)
     */
    private $telCambio;

    /**
     * @var string
     *
     * @ORM\Column(name="representante_cambio", type="string", length=500, nullable=true)
     */
    private $representanteCambio;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_cambio", type="string", length=250, nullable=true)
     */
    private $mailCambio;

    /**
     * @var string
     *
     * @ORM\Column(name="password_cambio", type="string", length=15, nullable=true)
     */
    private $passwordCambio;

    /**
     * @var string
     *
     * @ORM\Column(name="ciudad_cambio", type="string", length=70, nullable=true)
     */
    private $ciudadCambio;

    /**
     * @var integer
     *
     * @ORM\Column(name="aprobacion_cambio", type="integer", nullable=true)
     */
    private $aprobacionCambio;


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
     * @return Usuario
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
     * @return Usuario
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
     * @return Usuario
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
     * Set tel
     *
     * @param string $tel
     * @return Usuario
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set representante
     *
     * @param string $representante
     * @return Usuario
     */
    public function setRepresentante($representante)
    {
        $this->representante = $representante;

        return $this;
    }

    /**
     * Get representante
     *
     * @return string 
     */
    public function getRepresentante()
    {
        return $this->representante;
    }

    /**
     * Set mail
     *
     * @param string $mail
     * @return Usuario
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set enabled
     *
     * @param integer $enabled
     * @return Usuario
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return integer 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     * @return Usuario
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
     * Set cambioClave
     *
     * @param \DateTime $cambioClave
     * @return AceptacionContrato
     */
    public function setCambioClave($cambioClave)
    {
        $this->cambioClave = $cambioClave;

        return $this;
    }

    /**
     * Get cambioClave
     *
     * @return \DateTime 
     */
    public function getCambioClave()
    {
        return $this->cambioClave;
    }

    /**
     * Set nombreCambio
     *
     * @param string $nombreCambio
     * @return Usuario
     */
    public function setNombreCambio($nombreCambio)
    {
        $this->nombreCambio = $nombreCambio;

        return $this;
    }

    /**
     * Get nombreCambio
     *
     * @return string 
     */
    public function getNombreCambio()
    {
        return $this->nombreCambio;
    }

    /**
     * Set direccionCambio
     *
     * @param string $direccionCambio
     * @return Usuario
     */
    public function setDireccionCambio($direccionCambio)
    {
        $this->direccionCambio = $direccionCambio;

        return $this;
    }

    /**
     * Get direccionCambio
     *
     * @return string 
     */
    public function getDireccionCambio()
    {
        return $this->direccionCambio;
    }

    /**
     * Set telCambio
     *
     * @param string $telCambio
     * @return Usuario
     */
    public function setTelCambio($telCambio)
    {
        $this->telCambio = $telCambio;

        return $this;
    }

    /**
     * Get telCambio
     *
     * @return string 
     */
    public function getTelCambio()
    {
        return $this->telCambio;
    }

    /**
     * Set representanteCambio
     *
     * @param string $representanteCambio
     * @return Usuario
     */
    public function setRepresentanteCambio($representanteCambio)
    {
        $this->representanteCambio = $representanteCambio;

        return $this;
    }

    /**
     * Get representanteCambio
     *
     * @return string 
     */
    public function getRepresentanteCambio()
    {
        return $this->representanteCambio;
    }

    /**
     * Set mailCambio
     *
     * @param string $mailCambio
     * @return Usuario
     */
    public function setMailCambio($mailCambio)
    {
        $this->mailCambio = $mailCambio;

        return $this;
    }

    /**
     * Get mailCambio
     *
     * @return string 
     */
    public function getMailCambio()
    {
        return $this->mailCambio;
    }

    /**
     * Set passwordCambio
     *
     * @param string $passwordCambio
     * @return Usuario
     */
    public function setPasswordCambio($passwordCambio)
    {
        $this->passwordCambio = $passwordCambio;

        return $this;
    }

    /**
     * Get passwordCambio
     *
     * @return string 
     */
    public function getPasswordCambio()
    {
        return $this->passwordCambio;
    }

    /**
     * Set ciudadCambio
     *
     * @param string $ciudadCambio
     * @return Usuario
     */
    public function setCiudadCambio($ciudadCambio)
    {
        $this->ciudadCambio = $ciudadCambio;

        return $this;
    }

    /**
     * Get ciudadCambio
     *
     * @return string 
     */
    public function getCiudadCambio()
    {
        return $this->ciudadCambio;
    }

    /**
     * Set aprobacionCambio
     *
     * @param integer $aprobacionCambio
     * @return Usuario
     */
    public function setAprobacionCambio($aprobacionCambio)
    {
        $this->aprobacionCambio = $aprobacionCambio;

        return $this;
    }

    /**
     * Get aprobacionCambio
     *
     * @return integer 
     */
    public function getAprobacionCambio()
    {
        return $this->aprobacionCambio;
    }
}

