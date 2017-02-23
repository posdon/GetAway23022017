<?php

namespace Mb\UserBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use FR3D\LdapBundle\Model\LdapUserInterface as LdapUserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user",uniqueConstraints={@ORM\UniqueConstraint(name="student_id", columns={"student_id"})})
 * @ORM\Entity(repositoryClass="Mb\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser implements LdapUserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="student_id", type="integer")
     */
    private $studentId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;
    
     /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
	
	 /**
     * @var boolean
     *
     * @ORM\Column(name="excludeFromAlgo", type="boolean")
     */
    private $excludeFromAlgo = false;

	/**
     * @var boolean
     *
     * @ORM\Column(name="isUpdate", type="boolean")
     */
    private $isUpdate = false;	
	
    /**
     * @var integer
     *
     * @ORM\Column(name="year_study", type="integer")
     */
    private $yearStudy;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank", type="integer")
     */
    private $rank;

    /**
     * @var double
     *
     * @ORM\Column(name="mean", type="decimal", precision=6, scale=2)
     */
    private $mean;


    /**
     * @ORM\ManyToOne(targetEntity="Mb\MainBundle\Entity\Department")
     * @ORM\JoinColumn(nullable=false)
    */
    private $department;
    
    /**
     * @ORM\OneToMany(targetEntity="Mb\AffectBundle\Entity\Wish", mappedBy="user", cascade={"remove"})
	 * @ORM\OrderBy({"priority" = "ASC"})
    */
    private $wishes; 
    
    /**
    * @ORM\OneToMany(targetEntity="Mb\FileBundle\Entity\FileSend", mappedBy="user", cascade={"remove"})
    */
    private $filesSend; 
    
    /**
    * @ORM\OneToOne(targetEntity="Mb\AffectBundle\Entity\Placement", inversedBy="user")
	* @ORM\JoinColumn(nullable=true)
    */
    private $placement; 
    
    
 
    

    
    /* LDAP attributes */

    /**
     * @var string
     *
     * @ORM\Column(name="dn", type="string", length=255)
     */
    protected $dn;
    

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
     * Set studentId
     *
     * @param integer $studentId
     *
     * @return User
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;

        return $this;
    }

    /**
     * Get studentId
     *
     * @return integer
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
	
	
	 /**
     * Set excludeFromAlgo
     *
     * @param boolean $excludeFromAlgo
     *
     * @return User
     */
    public function setExcludeFromAlgo($excludeFromAlgo)
    {
        $this->excludeFromAlgo = $excludeFromAlgo;

        return $this;
    }

    /**
     * Get excludeFromAlgo
     *
     * @return boolean
     */
    public function getExcludeFromAlgo()
    {
        return $this->excludeFromAlgo;
    }

	/**
     * Set isUpdate
     *
     * @param boolean $isUpdate
     *
     * @return User
     */
    public function setIsUpdate($isUpdate)
    {
        $this->isUpdate = $isUpdate;

        return $this;
    }

    /**
     * Get isUpdate
     *
     * @return boolean
     */
    public function getIsUpdate()
    {
        return $this->isUpdate;
    }
	
    /**
     * Set yearStudy
     *
     * @param integer $yearStudy
     *
     * @return User
     */
    public function setYearStudy($yearStudy)
    {
        $this->yearStudy = $yearStudy;

        return $this;
    }

    /**
     * Get yearStudy
     *
     * @return integer
     */
    public function getYearStudy()
    {
        return $this->yearStudy;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     *
     * @return User
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set mean
     *
     * @param double $mean
     *
     * @return User
     */
    public function setMean($mean)
    {
        $this->mean = $mean;

        return $this;
    }

    /**
     * Get mean
     *
     * @return double
     */
    public function getMean()
    {
        return $this->mean;
    }

    /**
     * Set department
     *
     * @param \Mb\MainBundle\Entity\Department $department
     *
     * @return User
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
     * Add wish
     *
     * @param \Mb\AffectBundle\Entity\Wish $wish
     *
     * @return User
     */
    public function addWish(\Mb\AffectBundle\Entity\Wish $wish)
    {
        $this->wishes[] = $wish;
		$wish->setUser($this);
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
     * Add filesSend
     *
     * @param \Mb\FileBundle\Entity\FileSend $filesSend
     *
     * @return User
     */
    public function addFilesSend(\Mb\FileBundle\Entity\FileSend $filesSend)
    {
        $this->filesSend[] = $filesSend;
		$filesSend->setUser($this);
        return $this;
    }

    /**
     * Remove filesSend
     *
     * @param \Mb\FileBundle\Entity\FileSend $filesSend
     */
    public function removeFilesSend(\Mb\FileBundle\Entity\FileSend $filesSend)
    {
        $this->filesSend->removeElement($filesSend);
    }

    /**
     * Get filesSend
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilesSend()
    {
        return $this->filesSend;
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


    /**
     * Set dn
     *
     * @param string $dn
     *
     * @return User
     */
    public function setDn($dn)
    {
        $this->dn = $dn;

        return $this;
    }

    /**
     * Get dn
     *
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }


    /**
     * For the Student given create a string with the complete year of the student
     * examples : "4INFO", "3SGM", ...
     *
     * @return string $values string of the complete promotion
     */ 
    public function createCompleteYear()
    {
      return $this->getYearStudy() . $this->getDepartment()->getName();
    }



    /**
     * Set placement
     *
     * @param \Mb\AffectBundle\Entity\Placement $placement
     *
     * @return User
     */
    public function setPlacement(\Mb\AffectBundle\Entity\Placement $placement = null)
    {
		if(!$this->getExcludeFromAlgo()){
			$this->placement = $placement;
			$placement->setUser($this);
		}
        return $this;
    }

    /**
     * Get placement
     *
     * @return \Mb\AffectBundle\Entity\Placement
     */
    public function getPlacement()
    {
        return $this->placement;
    }
	
	
    public function removePlacement()
    {
        $this->placement = null;
		return $this;
    }
}
