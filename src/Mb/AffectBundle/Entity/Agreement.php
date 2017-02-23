<?php

namespace Mb\AffectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Agreement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mb\AffectBundle\Entity\AgreementRepository")
 */
class Agreement
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
     * @var boolean
     *
     * @ORM\Column(name="validate", type="boolean")
     */
    private $validate;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="isECTS", type="boolean")
     */
    private $isECTS;

	/**
	 * @ORM\ManyToOne(targetEntity="Mb\AffectBundle\Entity\Placement", inversedBy="agreements")
     * @ORM\JoinColumn(nullable=false)
    */
	private $placement;
	
	/**
    * @ORM\OneToMany(targetEntity="Mb\AffectBundle\Entity\Subject", mappedBy="agreement", cascade={"persist","remove"})
	* @ORM\OrderBy({"name" = "ASC"})
    */
    private $subjects; 
	
	
	 /**
     * @var string
     *
     * @ORM\Column(name="com", type="text", nullable=true)
     */
    private $com;
	
	 /**
     * Set com
     *
     * @param string $com
     *
     * @return Commentary
     */
    public function setCom($com)
    {
        $this->com = $com;

        return $this;
    }

    /**
     * Get com
     *
     * @return string
     */
    public function getCom()
    {
        return $this->com;
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
     * Set isECTS
     *
     * @param boolean $isECTS
     *
     * @return Agreement
     */
    public function setIsECTS($isECTS)
    {
        $this->isECTS = $isECTS;

        return $this;
    }

    /**
     * Get isECTS
     *
     * @return boolean
     */
    public function getIsECTS()
    {
        return $this->isECTS;
    }
	
	
	 /**
     * Set placement
     *
     * @param \Mb\AffectBundle\Entity\Placement $placement
     *
     * @return Placement
     */
    public function setPlacement(\Mb\AffectBundle\Entity\Placement $placement)
    {
        $this->placement = $placement;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Mb\AffectBundle\Entity\Placement
     */
    public function getPlacement()
    {
        return $this->placement;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subject = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add subject
     *
     * @param \Mb\AffectBundle\Entity\Subject $subject
     *
     * @return Agreement
     */
    public function addSubject(\Mb\AffectBundle\Entity\Subject $subject)
    {
        $this->subject[] = $subject;
		$subject->setAgreement($this);
        return $this;
    }

    /**
     * Remove subject
     *
     * @param \Mb\AffectBundle\Entity\Subject $subject
     */
    public function removeSubject(\Mb\AffectBundle\Entity\Subject $subject)
    {
        $this->subject->removeElement($subject);
    }

    /**
     * Get subject
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get subjects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubjects()
    {
        return $this->subjects;
    }
}
