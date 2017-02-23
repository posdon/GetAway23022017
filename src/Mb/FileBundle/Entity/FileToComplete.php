<?php

namespace Mb\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FileToComplete
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mb\FileBundle\Entity\FileToCompleteRepository")
 */
class FileToComplete
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
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

	/**
	 * @ORM\ManyToMany(targetEntity="Mb\UniversityBundle\Entity\Specificity")
	*/
	private $specificities;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->specificities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set path
     *
     * @param string $path
     *
     * @return FileToComplete
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Add specificity
     *
     * @param \Mb\UniversityBundle\Entity\Specificity $specificity
     *
     * @return FileToComplete
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
}
