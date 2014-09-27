<?php

namespace ROV\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Winery
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Winery
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
     * @var integer
     * 
     * @ORM\ManyToOne(targetEntity="ROV\BlogBundle\Entity\Region", inversedBy="wineries")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity="ROV\BlogBundle\Entity\Wine", mappedBy="winery")
     */
    private $wines;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="postal", type="string", length=15, nullable=true)
     */
    private $postal;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=255, nullable=true)
     */
    private $web;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->wines = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Winery
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
     * Set region
     *
     * @param \ROV\BlogBundle\Entity\Region $region
     * @return Winery
     */
    public function setRegion(\ROV\BlogBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \ROV\BlogBundle\Entity\Region 
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Winery
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Winery
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set postal
     *
     * @param string $postal
     * @return Winery
     */
    public function setPostal($postal)
    {
        $this->postal = $postal;

        return $this;
    }

    /**
     * Get postal
     *
     * @return string 
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Winery
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Winery
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set web
     *
     * @param string $web
     * @return Winery
     */
    public function setWeb($web)
    {
        $this->web = $web;

        return $this;
    }

    /**
     * Get web
     *
     * @return string 
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * Add wines
     *
     * @param \ROV\BlogBundle\Entity\Wine $wines
     * @return Winery
     */
    public function addWine(\ROV\BlogBundle\Entity\Wine $wines)
    {
        $this->wines[] = $wines;

        return $this;
    }

    /**
     * Remove wines
     *
     * @param \ROV\BlogBundle\Entity\Wine $wines
     */
    public function removeWine(\ROV\BlogBundle\Entity\Wine $wines)
    {
        $this->wines->removeElement($wines);
    }

    /**
     * Get wines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWines()
    {
        return $this->wines;
    }
}
