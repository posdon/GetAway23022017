<?php

namespace Mb\AffectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subject
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mb\AffectBundle\Entity\SubjectRepository")
 */
class Subject
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
     * @var string
     *
     * @ORM\Column(name="mark", type="string", length=255, nullable=true)
     */
    private $mark;

	/**
	 * @ORM\ManyToOne(targetEntity="Mb\AffectBundle\Entity\Agreement", inversedBy="subjects")
     * @ORM\JoinColumn(nullable=false)
    */
	private $agreement;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="validate", type="boolean")
     */
    private $validate;
	
	/**
     * @var double
     *
     * @ORM\Column(name="mean", type="decimal", precision=6, scale=2)
     */
    private $ects;

	
	  /**
     * Set validate
     *
     * @param boolean $validate
     *
     * @return Agreement
     */
    public function setValidate($validate)
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * Get validate
     *
     * @return boolean
     */
    public function getValidate()
    {
        return $this->validate;
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
     * @return Subject
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
     * Set mark
     *
     * @param string $mark
     *
     * @return Subject
     */
    public function setMark($mark)
    {
        $this->mark = $mark;

        return $this;
    }

    /**
     * Get mark
     *
     * @return string
     */
    public function getMark()
    {
        return $this->mark;
    }

  

    /**
     * Set agreement
     *
     * @param \Mb\AffectBundle\Entity\Agreement $agreement
     *
     * @return Subject
     */
    public function setAgreement(\Mb\AffectBundle\Entity\Agreement $agreement)
    {
        $this->agreement = $agreement;

        return $this;
    }

    /**
     * Get agreement
     *
     * @return \Mb\AffectBundle\Entity\Agreement
     */
    public function getAgreement()
    {
        return $this->agreement;
    }

    /**
     * Set ects
     *
     * @param double $ects
     *
     * @return Subject
     */
    public function setEcts($ects)
    {
        $this->ects = $ects;

        return $this;
    }

    /**
     * Get ects
     *
     * @return double
     */
    public function getEcts()
    {
        return $this->ects;
    }
}
