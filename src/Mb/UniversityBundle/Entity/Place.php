<?php

namespace Mb\UniversityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Place
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mb\UniversityBundle\Entity\PlaceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Place
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
	//Valeur temporaire pour l'algorithme d'affectation
	/**
     * @var integer
     *
     * @ORM\Column(name="tempPlaces", type="integer", nullable=true)
     */
	private $tempPlaces;

    /**
     * @var integer
     *
     * @ORM\Column(name="places", type="integer")
     */
    private $places;

	/**
	 * @ORM\ManyToOne(targetEntity="Mb\UniversityBundle\Entity\University", inversedBy="places")
     * @ORM\JoinColumn(nullable=false)
    */
	private $university;
		
	
	/**
	 * @ORM\ManyToOne(targetEntity="Mb\MainBundle\Entity\Department")
     * @ORM\JoinColumn(nullable=false)
    */
	private $department;
	
	/**
	* @ORM\ManyToMany(targetEntity="Mb\UniversityBundle\Entity\MobilityPeriod")
	*/
	private $mobilityPeriod;
	
	//Valeur pour les pages d'universités
	/**
     * @var integer
     *
     * @ORM\Column(name="placesRest", type="integer", nullable=true)
     */
	private $placesRest;

	/**
     * @ORM\OneToMany(targetEntity="Mb\AffectBundle\Entity\Wish", mappedBy="place", cascade={"remove"})
	 * @ORM\JoinColumn(nullable=false)
    */
    private $wishes; 
	
	/**
    * @ORM\OneToMany(targetEntity="Mb\AffectBundle\Entity\Placement", mappedBy="place", cascade={"remove"})
	* @ORM\JoinColumn(nullable=true)
    */
    private $placements; 

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
     * @ORM\PostLoad
     */
	function loadFunction() {
		
		if($this->places == -1){
            $this->tempPlaces = PHP_INT_MAX;
        }else{
			$this->tempPlaces = $this->places;
			
		}
    }
	
	
	 /**
     * Set tempPlaces
     *
     * @param integer $tempPlaces
     *
     * @return Place
     */
	public function setTempPlaces($tempPlaces)
    {
        $this->tempPlaces = $tempPlaces;

        return $this;
    }

	/**
     * Get tempPlaces
     *
     * @return integer
     */
    public function getTempPlaces()
    {

        return $this->tempPlaces;
    }
	
	/**
     * Set placesRest
     *
     * @param integer $placesRest
     *
     * @return Place
     */
	public function setPlacesRest($placesRest)
    {
        $this->placesRest = $placesRest;

        return $this;
    }

	/**
     * Get placesRest
     *
     * @return integer
     */
    public function getPlacesRest()
    {
		 if($this->placesRest === null){
			 return $this->places;
		}
        return $this->placesRest;
    }
	

    /**
     * Set places
     *
     * @param integer $places
     *
     * @return Place
     */
    public function setPlaces($places)
    {
        if($places == PHP_INT_MAX || $places < -1)
            $places = -1;

        $this->places = $places;

        return $this;
    }

    /**
     * Get places
     *
     * @return integer
     */
    public function getPlaces()
    {
        if($this->places == -1)
            return PHP_INT_MAX;
        return $this->places;
    }


    /**
     * Get string to print places
     *
     * @return String
     */
    public function printPlaces()
    {
        if($this->places == -1)
            return "INF";
        return $this->places;
    }

    /**
     * Get string to print places in formularies
     *
     * @return String
     */
    public function printFormPlaces()
    {
        return $this->places;
    }

    /**
     * Set university
     *
     * @param \Mb\UniversityBundle\Entity\University $university
     *
     * @return Place
     */
    public function setUniversity(\Mb\UniversityBundle\Entity\University $university)
    {
        $this->university = $university;

        return $this;
    }

    /**
     * Get university
     *
     * @return \Mb\UniversityBundle\Entity\University
     */
    public function getUniversity()
    {
        return $this->university;
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
     * Set department
     *
     * @param \Mb\MainBundle\Entity\Department $department
     *
     * @return Place
     */
    public function setDepartment(\Mb\MainBundle\Entity\Department $department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Mb\MainBundle\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mobilityPeriod = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add mobilityPeriod
     *
     * @param \Mb\UniversityBundle\Entity\MobilityPeriod $mobilityPeriod
     *
     * @return Place
     */
    public function addMobilityPeriod(\Mb\UniversityBundle\Entity\MobilityPeriod $mobilityPeriod)
    {
        if(!in_array($mobilityPeriod,$this->mobilityPeriod->getValues()))
            $this->mobilityPeriod[] = $mobilityPeriod;

        return $this;
    }

    /**
     * Remove mobilityPeriod
     *
     * @param \Mb\UniversityBundle\Entity\MobilityPeriod $mobilityPeriod
     */
    public function removeMobilityPeriod(\Mb\UniversityBundle\Entity\MobilityPeriod $mobilityPeriod)
    {
        $this->mobilityPeriod->removeElement($mobilityPeriod);
    }

    /**
     * Add wish
     *
     * @param \Mb\AffectBundle\Entity\Wish $wish
     *
     * @return Place
     */
    public function addWish(\Mb\AffectBundle\Entity\Wish $wish)
    {
        $this->wishes[] = $wish;

        return $this;
    }

    /**
     * Remove wish
     *
     * @param \Mb\AffectBundle\Entity\Wish $wish
     */
    public function removeWish(\Mb\AffectBundle\Entity\Wish $wish)
    {
        $this->wishes->removeElement($wish);
    }

    /**
     * Get wishes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWishes()
    {
        return $this->wishes;
    }

    /**
     * Add placement
     *
     * @param \Mb\AffectBundle\Entity\Placement $placement
     *
     * @return Place
     */
    public function addPlacement(\Mb\AffectBundle\Entity\Placement $placement)
    {
        $this->placements[] = $placement;

        return $this;
    }

    /**
     * Remove placement
     *
     * @param \Mb\AffectBundle\Entity\Placement $placement
     */
    public function removePlacement(\Mb\AffectBundle\Entity\Placement $placement)
    {
        $this->placements->removeElement($placement);
    }

    /**
     * Get placements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlacements()
    {
        return $this->placements;
    }
}
