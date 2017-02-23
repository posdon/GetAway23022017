<?php

namespace Mb\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Department
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mb\MainBundle\Entity\DepartmentRepository")
 */
class Department
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

	/**
    * @var boolean
    *
    * @ORM\Column(name="bolt", type="boolean")
    */
    private $bolt = false;
	
	/* Défini quel algorithme d'affectation est utilisé, voir UserRepository */
	 /**
     * @var integer
     *
     * @ORM\Column(name="algo", type="integer")
     */
    private $algo;
	
	
	/**
     * Set algo
     *
     * @param integer $algo
     *
     * @return Department
     */
    public function setAlgo($algo)
    {
        $this->algo = $algo;

        return $this;
    }

    /**
     * Get algo
     *
     * @return integer
     */
    public function getAlgo()
    {
        return $this->algo;
    }
	

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
     * Set name
     *
     * @param string $name
     *
     * @return Department
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set bolt
     *
     * @param boolean $bolt
     *
     * @return Department
     */
    public function setBolt($bolt)
    {
        $this->bolt = $bolt;

        return $this;
    }

    /**
     * Get bolt
     *
     * @return boolean
     */
    public function getBolt()
    {
        return $this->bolt;
    }
	


	
}
