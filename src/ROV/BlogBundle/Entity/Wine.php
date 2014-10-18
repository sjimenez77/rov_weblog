<?php

namespace ROV\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Wine
 *
 * @ORM\Table(name="Wine")
 * @ORM\Entity
 * @Vich\Uploadable
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
     * @ORM\ManyToOne(targetEntity="ROV\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $brand;

    /**
     * @var integer
     * 
     * @ORM\ManyToOne(targetEntity="ROV\BlogBundle\Entity\Winery", inversedBy="wines")
     * @ORM\JoinColumn(name="winery_id", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\NotNull()
     */
    private $winery;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=3)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @var File $imageFile
     *
     * @Vich\UploadableField(mapping="wine_image", fileNameProperty="imageName")
     *
     * This is not a mapped field of entity metadata, just a simple property.
     */
    private $imageFile;

    /**
     * @var string $imageName
     *
     * @ORM\Column(type="string", length=255, name="image_name", nullable=true)
     */
    private $imageName;

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
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0,
     *      max = 100
     * )
     */
    private $points;

    /**
     * @var integer
     *
     * @ORM\Column(name="alcohol", type="decimal", precision=3, scale=1, nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 100
     * )
     */
    private $alcohol;

    /**
     * @var string
     *
     * @ORM\Column(name="wine_making", type="string", length=255, nullable=true)
     */
    private $wineMaking;

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
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="smallint")
     * @Assert\NotBlank()
     */
    private $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

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
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
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
     * Set author
     *
     * @param string $author
     * @return Blog
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
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
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(File $image)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
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
     * Set alcohol
     *
     * @param string $alcohol
     * @return Wine
     */
    public function setAlcohol($alcohol)
    {
        $this->alcohol = $alcohol;

        return $this;
    }

    /**
     * Get alcohol
     *
     * @return string 
     */
    public function getAlcohol()
    {
        return $this->alcohol;
    }

    /**
     * Set wineMaking
     *
     * @param string $wineMaking
     * @return Wine
     */
    public function setWineMaking($wineMaking)
    {
        $this->wineMaking = $wineMaking;

        return $this;
    }

    /**
     * Get wineMaking
     *
     * @return string 
     */
    public function getWineMaking()
    {
        return $this->wineMaking;
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
     * Set created
     *
     * @param \DateTime $created
     * @return Blog
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Blog
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
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
