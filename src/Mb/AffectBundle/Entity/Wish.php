<?php

namespace Mb\AffectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wish
 *
 * @ORM\Table(name="wish",uniqueConstraints={@ORM\UniqueConstraint(name="prio_user_year", columns={"priority", "user_id","year_id"})})
 * @ORM\Entity(repositoryClass="Mb\AffectBundle\Entity\WishRepository")
 */
class Wish
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

	/**
	 * @ORM\ManyToOne(targetEntity="Mb\UniversityBundle\Entity\Place", inversedBy="wishes")
     * @ORM\JoinColumn(nullable=false)
    */
	private $place;
	

	/**
	 * @ORM\ManyToOne(targetEntity="Mb\UserBundle\Entity\User", inversedBy="wishes")
     * @ORM\JoinColumn(  name="user_id", nullable=false)
    */
	private $user;
	
	 /**
	 * @ORM\ManyToOne(targetEntity="Mb\MainBundle\Entity\Year")
     * @ORM\JoinColumn(name="year_id",nullable=false)
    */
	private $year;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Mb\UniversityBundle\Entity\MobilityPeriod")
     * @ORM\JoinColumn(nullable=false)
    */
	private $mobilityPeriod;
	

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
     * Set priority
     *
     * @param integer $priority
     *
     * @return Wish
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

   

    /**
     * Set user
     *
     * @param \Mb\UserBundle\Entity\User $user
     *
     * @return Wish
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
     * Set year
     *
     * @param \Mb\MainBundle\Entity\Year $year
     *
     * @return Wish
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
     * Set place
     *
     * @param \Mb\UniversityBundle\Entity\Place $place
     *
     * @return Wish
     */
    public function setPlace(\Mb\UniversityBundle\Entity\Place $place)
    {
        $this->place = $place;
		$place->addWish($this);
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
     * Set mobilityPeriod
     *
     * @param \Mb\UniversityBundle\Entity\MobilityPeriod $mobilityPeriod
     *
     * @return Wish
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
}
