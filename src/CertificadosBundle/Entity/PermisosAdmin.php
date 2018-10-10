<?php

namespace CertificadosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PermisosAdmin
 *
 * @ORM\Table(name="permisos_admin", indexes={@ORM\Index(name="menu_admin_id", columns={"menu_admin_id"})})
 * @ORM\Entity
 */
class PermisosAdmin
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
     * @var integer
     *
     * @ORM\Column(name="administrador_id", type="bigint", nullable=false)
     */
    private $administradorId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tipo_permiso", type="boolean", nullable=false)
     */
    private $tipoPermiso;

    /**
     * @var \MenuAdmin
     *
     * @ORM\ManyToOne(targetEntity="MenuAdmin")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="menu_admin_id", referencedColumnName="id")
     * })
     */
    private $menuAdmin;



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
     * Set administradorId
     *
     * @param integer $administradorId
     *
     * @return PermisosAdmin
     */
    public function setAdministradorId($administradorId)
    {
        $this->administradorId = $administradorId;

        return $this;
    }

    /**
     * Get administradorId
     *
     * @return integer
     */
    public function getAdministradorId()
    {
        return $this->administradorId;
    }

    /**
     * Set tipoPermiso
     *
     * @param boolean $tipoPermiso
     *
     * @return PermisosAdmin
     */
    public function setTipoPermiso($tipoPermiso)
    {
        $this->tipoPermiso = $tipoPermiso;

        return $this;
    }

    /**
     * Get tipoPermiso
     *
     * @return boolean
     */
    public function getTipoPermiso()
    {
        return $this->tipoPermiso;
    }

    /**
     * Set menuAdmin
     *
     * @param \CertificadosBundle\Entity\MenuAdmin $menuAdmin
     *
     * @return PermisosAdmin
     */
    public function setMenuAdmin(\CertificadosBundle\Entity\MenuAdmin $menuAdmin = null)
    {
        $this->menuAdmin = $menuAdmin;

        return $this;
    }

    /**
     * Get menuAdmin
     *
     * @return \CertificadosBundle\Entity\MenuAdmin
     */
    public function getMenuAdmin()
    {
        return $this->menuAdmin;
    }
}
