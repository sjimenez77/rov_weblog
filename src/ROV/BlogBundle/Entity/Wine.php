<?php

namespace ROV\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wine
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Wine
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
     * @ORM\Column(name="brand", type="string", length=255)
     */
    private $brand;

    /**
     * @var integer
     * 
     * @ORM\ManyToOne(targetEntity="ROV\BlogBundle\Entity\Winery", inversedBy="wines")
     * @ORM\JoinColumn(name="winery_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $winery;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=3)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="varieties", type="string", length=255, nullable=true)
     */
    private $varieties;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="smallint")
     */
    private $points;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="smallint")
     */
    private $year;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->published = false;
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
     * Set brand
     *
     * @param string $brand
     * @return Wine
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return string 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set winery
     *
     * @param \ROV\BlogBundle\Entity\Winery $winery
     * @return Wine
     */
    public function setWinery(\ROV\BlogBundle\Entity\Winery $winery = null)
    {
        $this->winery = $winery;

        return $this;
    }

    /**
     * Get winery
     *
     * @return \ROV\BlogBundle\Entity\Winery 
     */
    public function getWinery()
    {
        return $this->winery;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Wine
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set varieties
     *
     * @param string $varieties
     * @return Wine
     */
    public function setVarieties($varieties)
    {
        $this->varieties = $varieties;

        return $this;
    }

    /**
     * Get varieties
     *
     * @return string 
     */
    public function getVarieties()
    {
        return $this->varieties;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return Wine
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Wine
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
     * Set description
     *
     * @param string $description
     * @return Wine
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Wine
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Article
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }
}
