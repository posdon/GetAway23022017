<?php

namespace Mb\AffectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Placement
 *
 * @ORM\Table(name="placement")
 * @ORM\Entity(repositoryClass="Mb\AffectBundle\Entity\PlacementRepository")
 */
class Placement
{
     /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
	
    /**
     * @var string
     *
     * @ORM\Column(name="com", type="string", length=255, nullable=true)
     */
    private $com;
	
	 /**
     * @var integer
     *
     * @ORM\Column(name="temp", type="integer")
     */
    private $temp ;
	//0 => definitif (Valider RI), 1=>temporaire fixer par RI, 2=> temporaire suite à l'algo d'affectation (effacer à chaque relance de l'algo si = 2)
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="Mb\UniversityBundle\Entity\Place", inversedBy="placements")
     * @ORM\JoinColumn(nullable=false)
    */
	private $place;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Mb\MainBundle\Entity\Year",)
     * @ORM\JoinColumn(name="year_id",nullable=false)
    */
	private $year;
	
	/**
	 * @ORM\OneToOne(targetEntity="Mb\UserBundle\Entity\User", mappedBy="placement")
    */
	private $user;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Mb\UniversityBundle\Entity\MobilityPeriod")
     * @ORM\JoinColumn(nullable=false)
    */
	private $mobilityPeriod;

        
    /**
    * @ORM\OneToMany(targetEntity="Mb\AffectBundle\Entity\Agreement", mappedBy="placement", cascade={"persist","remove"})
	* @ORM\OrderBy({"validate" = "ASC"})
    */
    private $agreements; 
    
	
	
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
     * Set com
     *
     * @param string $com
     *
     * @return Placement
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
     * Set year
     *
     * @param \Mb\MainBundle\Entity\Year $year
     *
     * @return Placement
     */
    public function setYear(\Mb\MainBundle\Entity\Year $year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return \Mb\MainBundle\Entity\Year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set user
     *
     * @param \Mb\UserBundle\Entity\User $user
     *
     * @return Placement
     */
    public function setUser(\Mb\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Mb\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set mobilityPeriod
     *
     * @param \Mb\UniversityBundle\Entity\MobilityPeriod $mobilityPeriod
     *
     * @return Placement
     */
    public function setMobilityPeriod(\Mb\UniversityBundle\Entity\MobilityPeriod $mobilityPeriod)
    {
        $this->mobilityPeriod = $mobilityPeriod;

        return $this;
    }

    /**
     * Get mobilityPeriod
     *
     * @return \Mb\UniversityBundle\Entity\MobilityPeriod
     */
    public function getMobilityPeriod()
    {
        return $this->mobilityPeriod;
    }

    /**
     * Set place
     *
     * @param \Mb\UniversityBundle\Entity\Place $place
     *
     * @return Placement
     */
    public function setPlace(\Mb\UniversityBundle\Entity\Place $place)
    {
        $this->place = $place;
		$place->addPlacement($this);
        return $this;
    }

    /**
     * Get place
     *
     * @return \Mb\UniversityBundle\Entity\Place
     */
    public function getPlace()
    {
        return $this->place;
    }
	
	/**
     * Set temp
     *
     * @param integer $temp
     *
     * @return Placement
     */
    public function setTemp($temp)
    {
        $this->temp = $temp;

        return $this;
    }

    /**
     * Get temp
     *
     * @return integer
     */
    public function getTemp()
    {
        return $this->temp;
    }


    /**
     * Get status describing the actual placement
     *
     * @return string between "Definitif","Valide","Temporaire"
     */
    public function getStatus()
    {
        switch ($this->temp) {
            case 0:
                return "Définitif";
            case 1:
                return "Valide";
            default:
                return "Temporaire";
        }
    }
	
	
	
	 /**
     * Add agreement
     *
     * @param \Mb\AffectBundle\Entity\Agreement $agreement
     *
     * @return User
     */
    public function addAgreement(\Mb\AffectBundle\Entity\Agreement $agreement)
    {
        $this->agreements[] = $agreement;
		$agreement->setPlacement($this);
        return $this;
    }

    /**
     * Remove agreement
     *
     * @param \Mb\AffectBundle\Entity\Agreement $agreement
     */
    public function removeAgreement(\Mb\AffectBundle\Entity\Agreement $agreement)
    {
        $this->agreements->removeElement($agreement);
    }

    /**
     * Get agreements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgreements()
    {
        return $this->agreements;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->agreements = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
