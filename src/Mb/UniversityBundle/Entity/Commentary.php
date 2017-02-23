<?php

namespace Mb\UniversityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentary
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mb\UniversityBundle\Entity\CommentaryRepository")
 */
class Commentary
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;
	
	 /**
     * @var string
     *
     * @ORM\Column(name="com", type="text")
     */
    private $com;

	/**
	 * @ORM\ManyToOne(targetEntity="Mb\UniversityBundle\Entity\University", inversedBy="commentaries")
     * @ORM\JoinColumn(nullable=false)
    */
	private $university;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Mb\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
    */
	private $user;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Commentary
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

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
     * Set university
     *
     * @param \Mb\UniversityBundle\Entity\University $university
     *
     * @return Commentary
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
     * Set user
     *
     * @param \Mb\UserBundle\Entity\User $user
     *
     * @return Commentary
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
}
