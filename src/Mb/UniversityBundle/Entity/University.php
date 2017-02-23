<?php

namespace Mb\UniversityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * University
 *
 * @ORM\Table(name="university",uniqueConstraints={@ORM\UniqueConstraint(name="name_country", columns={"name","country_id"})})
 * @ORM\Entity(repositoryClass="Mb\UniversityBundle\Entity\UniversityRepository")
 */
class University
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
	 * @ORM\ManyToOne(targetEntity="Mb\UniversityBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id",nullable=false)
    */
	private $country;
	
	/**
	* @ORM\OneToMany(targetEntity="Mb\UniversityBundle\Entity\Commentary", mappedBy="university")
	*/
	private $commentaries; 

	/**
	 * @ORM\ManyToMany(targetEntity="Mb\UniversityBundle\Entity\Specificity")
	*/
	private $specificities;
	
	/**
	* @ORM\OneToMany(targetEntity="Mb\UniversityBundle\Entity\Place", mappedBy="university")
	*/
	private $places; 
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commentaries = new \Doctrine\Common\Collections\ArrayCollection();
        $this->specificities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->places = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return University
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
     * Set country
     *
     * @param \Mb\UniversityBundle\Entity\Country $country
     *
     * @return University
     */
    public function setCountry(\Mb\UniversityBundle\Entity\Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Mb\UniversityBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Add commentary
     *
     * @param \Mb\UniversityBundle\Entity\Commentary $commentary
     *
     * @return University
     */
    public function addCommentary(\Mb\UniversityBundle\Entity\Commentary $commentary)
    {
        $this->commentaries[] = $commentary;
		$commentary->setUniversity($this);
        return $this;
    }

    /**
     * Remove commentary
     *
     * @param \Mb\UniversityBundle\Entity\Commentary $commentary
     */
    public function removeCommentary(\Mb\UniversityBundle\Entity\Commentary $commentary)
    {
        $this->commentaries->removeElement($commentary);
    }

    /**
     * Get commentaries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommentaries()
    {
        return $this->commentaries;
    }

    /**
     * Add specificity
     *
     * @param \Mb\UniversityBundle\Entity\Specificity $specificity
     *
     * @return University
     */
    public function addSpecificity(\Mb\UniversityBundle\Entity\Specificity $specificity)
    {
        $this->specificities[] = $specificity;

        return $this;
    }

    /**
     * Remove specificity
     *
     * @param \Mb\UniversityBundle\Entity\Specificity $specificity
     */
    public function removeSpecificity(\Mb\UniversityBundle\Entity\Specificity $specificity)
    {
        $this->specificities->removeElement($specificity);
    }

    /**
     * Get specificities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSpecificities()
    {
        return $this->specificities;
    }

    /**
     * Add place
     *
     * @param \Mb\UniversityBundle\Entity\Place $place
     *
     * @return University
     */
    public function addPlace(\Mb\UniversityBundle\Entity\Place $place)
    {
        $this->places[] = $place;
		$place->setUniversity($this);
        return $this;
    }

    /**
     * Remove place
     *
     * @param \Mb\UniversityBundle\Entity\Place $place
     */
    public function removePlace(\Mb\UniversityBundle\Entity\Place $place)
    {
        $this->places->removeElement($place);
    }

    /**
     * Get places
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlaces()
    {
        return $this->places;
    }
}
