<?php

namespace ROV\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Region
 *
 * @ORM\Table(name="Region")
 * @ORM\Entity
 */
class Region
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
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2)
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="ROV\BlogBundle\Entity\Winery", mappedBy="region")
     */
    private $wineries;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->wineries = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Region
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
     * Set slug
     *
     * @param string $slug
     * @return Region
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Region
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Add wineries
     *
     * @param \ROV\BlogBundle\Entity\Winery $wineries
     * @return Region
     */
    public function addWinery(\ROV\BlogBundle\Entity\Winery $wineries)
    {
        $this->wineries[] = $wineries;

        return $this;
    }

    /**
     * Remove wineries
     *
     * @param \ROV\BlogBundle\Entity\Winery $wineries
     */
    public function removeWinery(\ROV\BlogBundle\Entity\Winery $wineries)
    {
        $this->wineries->removeElement($wineries);
    }

    /**
     * Get wineries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWineries()
    {
        return $this->wineries;
    }
}
