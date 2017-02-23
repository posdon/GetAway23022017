<?php

namespace Mb\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * FileSend
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mb\FileBundle\Entity\FileSendRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class FileSend
{
	 /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $ancientName;

	 /**
     * @var boolean
     *
     * @ORM\Column(name="sign", type="boolean")
     */
    private $sign = false;

    /**
	 * @ORM\ManyToOne(targetEntity="Mb\MainBundle\Entity\Department")
     * @ORM\JoinColumn(nullable=false)
    */
	private $department;
	
		
	 /**
	 * @ORM\ManyToOne(targetEntity="Mb\MainBundle\Entity\Year")
     * @ORM\JoinColumn(nullable=false)
    */
	private $year;
	
		/**
	 * @ORM\ManyToOne(targetEntity="Mb\UserBundle\Entity\User", inversedBy="filesSend")
     * @ORM\JoinColumn(nullable=false)
    */
	private $user;
	
	 /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
	
	/**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

	
	

	public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

	
	/**
     * Get sign
     *
     * @return string
     */
	public function getPath()
	{
		return $this->path;
	}
	
	/**
     * Set sign
     *
     * @param string $sign
     *
     * @return FileSend
     */
	public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
	
    /**
     * Set sign
     *
     * @param boolean $sign
     *
     * @return FileSend
     */
    public function setSign($sign)
    {
        $this->sign = $sign;

        return $this;
    }

    /**
     * Get sign
     *
     * @return boolean
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * Set department
     *
     * @param \Mb\MainBundle\Entity\Department $department
     *
     * @return FileSend
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
     * Set year
     *
     * @param \Mb\MainBundle\Entity\Year $year
     *
     * @return FileSend
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
     * @return FileSend
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
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
	
	public function upload()
	{
		// the file property can be empty if the field is not required
		if (null === $this->getFile()) {
			return;
		}

		// move takes the target directory and then the
		// target filename to move to
		$this->getFile()->move(
			$this->getUploadRootDir(),
			$this->getFile()->getClientOriginalName()
		);

		// set the path property to the filename where you've saved the file
		$this->path = $this->getFile()->getClientOriginalName();
		$this->ancientName = $this->getFile()->getClientOriginalName();
		// clean up the file property as you won't need it anymore
		$this->file = null;
	}
	
	
	 /**
     * @ORM\PreRemove
     */
	 public function deleteFile()
	 {
		 $fs = new Filesystem();
		 $fs->remove($this->getAbsolutePath());
	 }
	 
	 /**
     * Get date.
     *
     * @return datetime
     */
	public function getDate()
    {
        return $this->date;
    }
	
	/**
     * Set date.
     *
     * @return datetime
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }
	
	 /**
     * Get ancientName.
     *
     * @return string
     */
	public function getAncientName()
    {
        return $this->ancientName;
    }
	
	/**
     * Set ancientName.
     *
     * @return string
     */
    public function setAncientName($ancientName)
    {
        $this->ancientName = $ancientName;
        return $this;
    }
	
}
