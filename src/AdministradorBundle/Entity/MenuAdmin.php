<?php

namespace AdministradorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuAdmin
 *
 * @ORM\Table(name="menu_admin")
 * @ORM\Entity
 */
class MenuAdmin
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
     * @var string
     *
     * @ORM\Column(name="descripcion_permiso", type="string", length=100, nullable=false)
     */
    private $descripcionPermiso;



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
     * Set descripcionPermiso
     *
     * @param string $descripcionPermiso
     *
     * @return MenuAdmin
     */
    public function setDescripcionPermiso($descripcionPermiso)
    {
        $this->descripcionPermiso = $descripcionPermiso;

        return $this;
    }

    /**
     * Get descripcionPermiso
     *
     * @return string
     */
    public function getDescripcionPermiso()
    {
        return $this->descripcionPermiso;
    }
}
