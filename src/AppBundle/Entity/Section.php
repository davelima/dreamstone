<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="sections")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SectionRepository")
 */
class Section
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $showOnMenu = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status = false;

    /**
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumn(name="parent_section", referencedColumnName="id")
     */
    private $parentSection;

    /**
     * @ORM\ManyToOne(targetEntity="Administrator")
     * @ORM\JoinColumn(name="author", referencedColumnName="id")
     */
    private $author;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

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
     * Set title
     *
     * @param string $title
     *
     * @return Section
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set showOnMenu
     *
     * @param boolean $showOnMenu
     *
     * @return Section
     */
    public function setShowOnMenu($showOnMenu)
    {
        $this->showOnMenu = $showOnMenu;

        return $this;
    }

    /**
     * Get showOnMenu
     *
     * @return boolean
     */
    public function getShowOnMenu()
    {
        return $this->showOnMenu;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Section
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set parentSection
     *
     * @param \AppBundle\Entity\Section $parentSection
     *
     * @return Section
     */
    public function setParentSection(\AppBundle\Entity\Section $parentSection = null)
    {
        $this->parentSection = $parentSection;

        return $this;
    }

    /**
     * Get parentSection
     *
     * @return \AppBundle\Entity\Section
     */
    public function getParentSection()
    {
        return $this->parentSection;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Section
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
     * Set author
     *
     * @param \AppBundle\Entity\Administrator $author
     *
     * @return Section
     */
    public function setAuthor(\AppBundle\Entity\Administrator $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\Administrator
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Section
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
}
